LOCAL_CODE_DIR=/Users/grantsun/Documents/php/english_dev
REMOTE_CODE_DIR=/var/www/dosight/english_dev
HOST=dev-minigrid
# warning: if english_dev exist on dest server, it will be cleaned.
# /usr/local/php7.2/bin/php /usr/bin/composer install
if test $# -eq 1;
then
   echo 'Do you want to reinit your website?[yes|no]';
   read n;
   if test $n == "yes" -o $n == "Yes";
   then
        ssh $HOST "rm -rf $REMOTE_CODE_DIR";
	echo "init.....";
	rsync -ua $LOCAL_CODE_DIR  $HOST:${REMOTE_CODE_DIR%/*}
	echo "done!";
	exit;
   fi;
fi;

srcSvnPath=$LOCAL_CODE_DIR
destSvnPath=$REMOTE_CODE_DIR
fswatch -l .4 $srcSvnPath |xargs -n1| while read srcFile; do
	destFile=$destSvnPath${srcFile/$srcSvnPath/};
        echo $destFile

	destFilePath=${destFile%/*}
#echo $destFile;
#echo $srcFile;
	if [[ $srcFile =~ "/.git/" ]]; then
		continue;
	fi;
	rsync -aqe ssh $srcFile $HOST:$destFile
        r=$?
	now=`date`
	if test $r -eq 0;
        then
		printf "$destFile was synced at \e[0;32m $now \e[0m\n"
	else
		printf "\e[0;31m $destFile was failed to sync!\e[0m\n";
	fi
done

