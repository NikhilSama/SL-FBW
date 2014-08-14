#!/bin/bash

pages=$(echo "select page_id from snaplion_page;" | mysql -h snaplion.cck6cwihhy4y.ap-southeast-1.rds.amazonaws.com -uroot -pSn@pDr@g0n6743 -B --disable-column-names SnapLion_FBW)

for page in $pages
do
    curl http://fbw.snaplion.com/cron.php?page_id=$page
done