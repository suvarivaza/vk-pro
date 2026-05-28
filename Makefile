
SSH_SERVER =  www-root@83.136.232.20
PATH_SITE = /var/www/www-root/data/www/vk-pro.top/
PATH_SITE_LOGS = /var/www/www-root/data/www/vk-pro.top/logs
PATH_ERRORS_LOG = /var/www/www-root/data/logs/vk-pro.top.error.log
PATH_ACCESS_LOG = /var/www/www-root/data/logs/vk-pro.top.access.log

.PHONY: deploy
deploy: ## deploy-force from git repo
	git fetch origin && git reset --hard origin/main

########## Logs ###########
## @echo ======= Logs =======

# чтение файла
.PHONY: read-all-logs
read-all-logs: ## read-all-logs
	tail -Fv -n 100 $(PATH_SITE_LOGS)/*.log

.PHONY: read-all-cron-logs
read-all-cron-logs: ## read-all-logs
	tail -Fv -n 100 $(PATH_SITE_LOGS)/crons/*.log

.PHONY: read-errors-log
read-errors-log: ## read file in realtime (first do: make ssh-connect)
	tail -f -n 100 -s 1 $(PATH_ERRORS_LOG)


.PHONY: delete-all-logs
delete-all-logs: ## delete-all-logs
	find $(PATH_SITE_LOGS) \
	-mindepth 1 \
	! -path "$(PATH_SITE_LOGS)/crons*" \
	-delete

#.PHONY: delete-all-logs
#delete-all-logs: ## delete-app-logs
#	rm -rf $(PATH_SITE_LOGS)/*




ssh-connect: ## connect to server by ssh
	ssh -t $(SSH_SERVER) 'cd $(PATH_SITE); bash'


########## Files ###########

ssh-download-archive: ## download archive tar.gz by ssh
	ssh $(SSH_SERVER) 'cd $(PATH_SITE) && tar --exclude='./img' -vczf - ./' >site.tar.gz

unzip-archive: ## unzip archive tar.gz
	tar -xvf site.tar.gz -C ./

ssh-download-files: ## download archive gzip by ssh and unzip immediately
	ssh $(SSH_SERVER) 'cd $(PATH_SITE) && tar --exclude='./img' -vczf - ./' | tar  xzf -

ssh-reload-files-only-one-folder: ## download files from specific dir by ssh
	ssh $(SSH_SERVER) 'cd $(PATH_SITE) && tar -vczf - ./storage/ai_images' | tar  xzf -


########## Database ###########

ssh-download-db: ## download db dump by ssh
	ssh $(SSH_SERVER) "mysqldump -u userName -p123456 dbName | gzip" > db.sql.gz
	gunzip db.sql.gz

ssh-import-db:
	ssh $(SSH_SERVER) 'mysql -u userName -p123455 dbName < db.sql'




#============= Help ===============#
.PHONY: help
help:
	@echo ======= Help =======
	@egrep -h '^[^[:blank:]].*\s##\s' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help