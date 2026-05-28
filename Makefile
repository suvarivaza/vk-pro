
server =  www-root@83.136.232.20
pathSite = /var/www/www-root/data/www/vk-pro.top/
pathErrorsLog = /var/www/www-root/data/logs/vk-pro.top.error.log
pathAccessLog = /var/www/www-root/data/logs/vk-pro.top.access.log


#screen -S download_site
#screen -x download_site


ssh-connect: ## connect to server by ssh
	ssh -t $(server) 'cd $(pathSite); bash'


ssh-download-archive: ## download archive tar.gz by ssh
	ssh $(server) 'cd $(pathSite) && tar --exclude='./img' -vczf - ./' >site.tar.gz


unzip-archive: ## unzip archive tar.gz
	tar -xvf site.tar.gz -C ./


ssh-download-files: ## download archive gzip by ssh and unzip immediately
	ssh $(server) 'cd $(pathSite) && tar --exclude='./img' -vczf - ./' | tar  xzf -


ssh-reload-files-only-one-folder: ## download files from specific dir by ssh
	ssh $(server) 'cd $(pathSite) && tar -vczf - ./storage/ai_images' | tar  xzf -


ssh-download-db: ## download db dump by ssh
	ssh $(server) "mysqldump -u userName -p123456 dbName | gzip" > db.sql.gz
	gunzip db.sql.gz


ssh-import-db:
	ssh $(server) 'mysql -u userName -p123455 dbName < db.sql'



########## Logs ###########
## @echo ======= Logs =======

read-server-errors-logs: ## read log file (first do: make ssh-connect)
	cat $(pathErrorsLog) | sort | uniq -c | sort -nr | head -n 15

read-server-errors-logs-real-time: ## read file in realtime (first do: make ssh-connect)
	tail -f -n 10 -s 1 $(pathErrorsLog)

find-file: ## find file by name (first do: make ssh-connect)
	find . -name "fileName"

search-in-files: ## find string in files (first do: make ssh-connect)
	grep -r "406182" /var/log/


show-top-ip: ## показать топ айпи адресов с большим количеством запросов (first do: make ssh-connect)
	grep "19/Aug/2023:02" $(pathAccessLog) | awk "{print $1}" | sort | uniq -c | sort -nr | head -n 10


show-count-requests: ## показать топ айпи адресов с большим количеством запросов
	grep -c "19/Aug/2023:02" $(pathAccessLog)


########## SECURITY ###########

# показать изменения в php файлах за последние n минут
# find /var/www/chatgpt/data/www/chatgpt4rus.ru/ -type f -mmin -14400 -iname '*.php*'
n = 60*24
.PHONY: show-changes-in-php-files
show-changes-in-php-files:
	ssh $(server) 'find $(pathSite) -type f -mmin -$(n) -iname '*.php*''

# показать изменения во всех файлах за последние n минут
n = 1440
.PHONY: show-changes-in-files
show-changes-in-files:
	ssh $(server) 'find $(pathSite) -type f -mmin -$(n)'



#============= Help ===============#
.PHONY: help
help:
	@echo ======= Help =======
	@egrep -h '^[^[:blank:]].*\s##\s' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help