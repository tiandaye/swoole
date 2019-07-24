echo "Reloading..."

# linux通过进程名获得pid
# cmd=$(pidof reload_master)

# mac
cmd=$(ps -A | grep -m1 reload_master | awk '{print $1}')

kill -USR1 "$cmd"
echo "Reloaded"