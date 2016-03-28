import requests
from time import sleep

def kick():
    data = {'secret': 'testing123'}
    r = requests.post('http://192.168.182.2/api/clear.php', data)
    print r.content

if __name__ == '__main__':
    while True:
        kick()
        sleep(60) # 1 minutes