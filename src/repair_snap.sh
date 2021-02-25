service plexmediaserver stop
rm /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db-shm
rm /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db-wal
cp /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db.original
sqlite3 /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db "DROP index 'index_title_sort_naturalsort'"
sqlite3 /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db "DELETE from schema_migrations where version='20180501000000'"
sqlite3 /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db .dump > ~/dump.sql
rm /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db
sqlite3 /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db < ~/dump.sql
chown plex:plex /var/snap/plexmediaserver/common/Library/Application\ Support/Plex\ Media\ Server/Plug-in\ Support/Databases/com.plexapp.plugins.library.db
service plexmediaserver start
rm ~/dump.sql
