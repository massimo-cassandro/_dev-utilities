#!/bin/bash

read -p "cartella: "  folder
echo "Cartella selezionata: $folder"


# file invisibili
cd "$folder"
listfiles=$(find . -type d -name "\.*" -not -name "." -not -name "..") #cartelle
while read f; do
    if [ -n "$f" ]; then
        dn="$(dirname "$f")"
        fn="$(basename "$f")"
        fm=_"${fn:1}"
        mv "$dn/$fn" "$dn/$fm"
    fi
done <<< "$listfiles"

listfiles=$(find . -type f -name "\.*") #file
while read f; do
    if [ -n "$f" ]; then
        dn="$(dirname "$f")"
        fn="$(basename "$f")"
        fm=_"${fn:1}"
        mv "$dn/$fn" "$dn/$fm"
    fi
done <<< "$listfiles"