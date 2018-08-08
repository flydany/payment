
#拉卡拉商户号：17717355563 【 大白钱包.源润 】 放款
*/10 * * * * /bin/mkdir -p /data/logs/crontab/paymax_withdraw_17717355563 ;/usr/local/php/bin/php /data/www/js_withdraw/yii withdraw/withdraw/withdraw 20 17717355563 >> /data/logs/crontab/paymax_withdraw_17717355563/`date +"\%Y\%m\%d"`.log 2>&1 &

#拉卡拉商户号：17717355563 【 大白钱包.源润  】 放款查询
*/10 * * * * /bin/mkdir -p /data/logs/crontab/paymax_withdraw_query_17717355563 ;/usr/local/php/bin/php /data/www/js_withdraw/yii withdraw/withdraw/query 20 17717355563 >> /data/logs/crontab/paymax_withdraw_query_17717355563/`date +"\%Y\%m\%d"`.log 2>&1 &
