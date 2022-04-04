from datetime import datetime, date
import time
import schedule
import pymysql
import socket
from scapy.all import ARP, Ether, srp
# log into our database with our protected config file, rather than have our credentials visible in this file
conn = pymysql.connect(read_default_file="/etc/my.cnf")
curr = conn.cursor()


# Connect to my database & set the insert query
print(curr.execute("SHOW DATABASES"))
tables = curr.fetchall()
sql = """insert into `test` (date, time, ip_address, device_name)
			values (%s, %s, %s, %s)
"""


def get_date():
    today = str(date.today())
    return today


def get_time():
    now = datetime.now()
    current_time = now.strftime("%H:%M:%S")
    return str(current_time)

# This function gets passed raw hostname data from collect_hosts and formats the hostname to be written to SQL
def treat_hostname(hostname):
    # remove [ from string and onwards
    sep = '['
    # remove any other symbols, output clean device name
    stripped_hostname = hostname.split(sep, 1)[0]
    treated_hostname = str(stripped_hostname.split("'")[1::2])
    return(str(treated_hostname.strip("[]'")))


def write_to_db(date, time, ip, host):
    curr.execute(sql, (date, time, ip, host))
    conn.commit()


def collect_hosts():
        # set network to scan
    target_ip = "192.168.20.1/24"
    this_date = get_date()
    this_time = get_time()
    # set network to ARP scan
    arp = ARP(pdst=target_ip)
    # set our ether address to be broadcast address
    ether = Ether(dst="ff:ff:ff:ff:ff:ff")
    # construct our packet to send
    packet = ether / arp
    # send our packet with a timeout of 3 to every address
    result = srp(packet, timeout=3)[0]

    clients = []
    # any client the ping was broadcast to is stored in the client array with IP and mac address
    for sent, received in result:
        clients.append({'ip': received.psrc, 'mac': received.hwsrc})
    # for every client in our array, store IP, treat the hostname and store under hostname
    # print out a list in console, and write to the DB
    for client in clients:
        ip = str(client['ip'])
        hostname = treat_hostname(str(socket.gethostbyaddr(client['ip'])))
        print("IP: " + ip + ", Host: " + hostname)
        write_to_db(this_date, this_time, ip, hostname)


# run a network scan and save to db every X minutes at the start of the minute, regardless of runtime
schedule.every(1).minutes.at(":00").do(collect_hosts)


while True:
    schedule.run_pending()
collect_hosts()
