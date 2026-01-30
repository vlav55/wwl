SEARCH_STRING="$1"

find . -name "pid.txt" -exec sh -c '
  for file; do
    if grep -q "'"$SEARCH_STRING"'" "$file"; then
      echo "Processing $file"
      cat "$file"
    fi
  done
' sh {} +

