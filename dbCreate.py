import sqlite3
import sys

def addUsers(cur, userName, password): 
	cur.execute("INSERT INTO users (username, password) VALUES ('%s', '%s');" % (userName, password))
	 
	
def addInventory(cur,part,brand,partNum,price): 
	cur.execute("INSERT INTO inventory (partType, brand, partNum, price) VALUES ('%s', '%s','%s', '%s');" % (part,brand,partNum,price))
	
	
def showTable(cur,table):
	cur.execute("SELECT * FROM '%s';" % table)
	
	rows = cur.fetchall()
	
	for row in rows:
		print(row)
			
			
def main(): 
	
	# creation of the db file
	conn = sqlite3.connect("atown2.db")
	cur = conn.cursor()
	
	# create tables needed for db
	#cur.execute("CREATE TABLE users(username VARCHAR(200),password VARCHAR(200));")
	#cur.execute("CREATE TABLE inventory(partType VARCHAR(200),brand VARCHAR(200), partNum VARCHAR(200), price VARCHAR(200));")
	
	# drop table code
	#cur.execute("DROP TABLE inventory;")
	
	#open txt file 
	f = open("inventory.txt", "r")
	lines = f.readlines()
	
	
	record = []
	for i in lines: 
		i = i.strip()
		part = i.split('|')
		#print(part[4])
		parts = {"part": part[0], "brand": part[1], "partNum": part[2], "price": part[3]}
		record.append(parts)
		
		
	#for info in record: 
		#addInventory(cur,info["part"], info["brand"], info["partNum"], info["price"])
		
	showTable(cur,'inventory')
	#addUsers(cur, "admin", "$2y$10$kJxisqFNpsTm007IUdoDUOZKnLI2E1ZdEF2gr/IVktVo0.JC//De.")
	
	# close connection 		
	conn.commit()
	conn.close()
	
main()

