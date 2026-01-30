cd /var/www/vlav/data/www/wwl/
find ./d -maxdepth 1 -type d ! -path "./d/1000" ! -path "./d/1000/*" >/tmp/target_dirs.txt
while read target_dir; do
    echo "=== Checking: $target_dir ==="
    sed 's|^\./d/1000/||' "scripts/sync_1000/sync_1000.txt" | while read rel_path; do
        source="./d/1000/$rel_path"
        dest="$target_dir/$rel_path"
        if [ -f "$source" ]; then
            #echo "  $source → $dest"
            mkdir -p "$(dirname "$dest")"
            cp -up "$source" "$dest"
        fi
    done
    echo ""
done < /tmp/target_dirs.txt
