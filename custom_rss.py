# libraries that need to be downloaded (preferably using pip)
from feedparser import *
from newspaper import *
import MySQLdb
import MySQLdb.cursors
import requests
import tldextract

# libraries that should be built into Python (don't need to be downloaded)
from urllib import parse as urlparse
import re
import html
import ssl

# I have no idea what this is or what it does but it makes this script work on Python 3.6 (
if hasattr(ssl, '_create_unverified_context'):
    ssl._create_default_https_context = ssl._create_unverified_context

# this is the database script for any custom RSS feeds we use
# needs to be run on the server periodically
# Defects so far: text scraping isn't perfect (might be due to paywalls or popups before page load) and some pages just flat out won't download (but they seem to be in the minority and that's good enough for us)

# Links in the RSS feed are generated by google, and the actual URL of the article is stored as the 'url' parameter in this link
# this function gets us the actual URL
def getURL(RSS_url):
    parsed = urlparse.urlparse(RSS_url)
    url = urlparse.parse_qs(parsed.query)['url'][0]
    return url

# 'Supreme Court' appears in the titles of the RSS feed with bold tags around them
# this function strips the HTML and returns a text-only version of the title
def cleanTitle(original_title):
    cleanr = re.compile('<.*?>')
    cleanTitle = html.unescape(re.sub(cleanr, '', original_title))
    return cleanTitle

# original date is a long date/time string
# for our purposes, we really only need date, not time - so this function extracts the date and converts it into a month/date/year format
def convertDate(orig_date):
    convertedDate = datetime.datetime.strptime(orig_date,"%Y-%m-%dT%H:%M:%SZ").strftime('%Y-%m-%d')
    return convertedDate

# parses URL to get the domain name of each article's link - the source
# one defect in handling the source is that, as of now, we don't know how to handle multiple-word sources beyond just storing it all as one string (so Fox News would just be stored as foxnews)
def getSource(url):
    ext = tldextract.extract(url)
    source = ext.domain
    return source

# Newspaper library can get grab keywords from articles in conjunction with the nltk (natural language toolkit) library
# this function prepares the article for language processing and returns an array of keywords from the article
def getKeywords(article):
    article.nlp()
    return article.keywords

# inserts an article's headline image into the database (since we haven't figured out yet how to avoid the useless images)
# for now this is just a website URL
def addImage(image,idArticle,c):
    c.execute("""INSERT INTO image(idArticle,path) VALUES (%s,%s)""",(idArticle, image))

# inserts keywords from the Article keyword array into the database one-by-one 
def addKeywords(keywords,idArticle,c):
    # if keyword is a first occurrence, insert it into article_keywords
    for key in keywords:
        if not KeywordIsDuplicate(key,c):
            c.execute("""INSERT INTO article_keywords(keyword) VALUES (%s)""",(key,))
        
        # connect the keyword to an article by inserting a keyword_instances entry    
        c.execute("""SELECT idKey FROM article_keywords WHERE keyword = %s""",(key,))
        row = c.fetchone()
        idKey = row['idKey']
        c.execute("""INSERT INTO keyword_instances(idArticle,idKey) VALUES (%s,%s)""",(idArticle,idKey))

# wrapper function
# adds all of an article's information to the database
def addToDatabase(url,source,author,date,text,title,keywords,image,c):
    # insert article information into Article table
    t = (url, source, author, date, text, title)
    c.execute(
        """INSERT INTO article(url, source, author, date, article_Text, title)
        VALUES (%s,%s,%s,%s,%s,%s)""",t)
    
    # then insert the other stuff (keywords and images)
    idArticle = c.lastrowid
    addKeywords(keywords,idArticle,c)
    if image != None:
        addImage(image,idArticle,c)

        
# checks whether the title of an article is already in the database, avoiding duplicates
# we only check for title because the likeliness of identical titles is crazy low, and it cuts down on reposts from other sites
def ArticleIsDuplicate(title,c):
    c.execute("""SELECT * FROM article WHERE title = %s""",(title,))
    if c.rowcount == 0:
        return False
    else:
        return True

# checks whether a keyword is already in the database
# same implemtnation as the article check, just specific to keywords
def KeywordIsDuplicate(key, c):
    c.execute("""SELECT * FROM article_keywords WHERE keyword = %s""",(key,))
    if c.rowcount == 0:
        return False
    else:
        return True
    
      
# goes through each entry in a given feed, check it for relevancy, and if relevant, add it to the database
# if we can't get the data from the website (403/404 errors, whatever) - an exception occurs, and we move to the next article
def parseFeed(RSS,c):
    # config info for Newspaper - keep article html for user-friendly display, set browser user agent to a desktop computer header to help fight 403 errors
    config = Config()
    config.keep_article_html = False
    config.browser_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9'

    # begin parsing
    feed = parse(RSS)
    total = len(feed.entries)
    successes = 0
    for post in feed.entries:
        url = getURL(post['link'])
        title = cleanTitle(post['title'])
        date = convertDate(post['date'])
        source = getSource(url)
        
        msg = url + '\n' + title + "\n"
        
        if ArticleIsDuplicate(title,c):
            msg += "Rejected - already in database"
        else: 
            # we'll do relevancy check sometime around here
            a = Article(url,config)
            try:
                a.download()
                a.parse()
                
                # since tiny articles are generally useless, we check for length here
                # this also helps us weed out paywalls, snippets, maybe even some local news sources
                text = a.text
                if len(text) > 500:
                    if(len(a.authors) > 0):
                        author = a.authors[0]
                    else:
                        author = 'Unknown'
                        
                    keywords = getKeywords(a)
                    if a.top_image == '':
                        image = None
                    else:
                        image = a.top_image
                    
                    addToDatabase(url,source,author,date,text,title,keywords,image,c)
                    successes += 1
                    msg += 'Added'
                
                else:
                    msg += 'Rejected - too short'
                
            except ArticleException:
                msg += 'Rejected - error occurred'
            
        print(msg + "\n")
        
    print(successes,"/",total,"articles added to database.")
    print('=======================================================')
        
def main():
    
    db = MySQLdb.connect(host="127.0.0.1",port=3306,user="root",password="",db="SupremeCourtApp",use_unicode=True,charset="utf8")
    db.autocommit(True)
    c = db.cursor(MySQLdb.cursors.DictCursor)
    
    #Google Alert custom feeds
    feeds = ['https://www.google.com/alerts/feeds/16346142240605984801/8005087395970124365','https://www.google.com/alerts/feeds/16346142240605984801/12974548777403563412']
    for feed in feeds:
        parseFeed(feed,c)
        
    c.close()
    db.close()

main()
    
    
