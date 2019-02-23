#!/bin/bash

# crea i link simbolici delle cartelle delle librerie condivise nella dir corrente

current_dir=$(pwd)

composer_folder=~/Sites/_libs/vendor
npm_folder=~/Sites/_libs/node_modules
_DEV_folder=~/Sites/_dev

echo '****' rimozione eventuali symlink già esistenti
rm -rf "$current_dir"/twig
rm -rf "$current_dir"/_dev
rm -rf "$current_dir"/node_modules

echo '****' creazione dir twig
mkdir -pv "$current_dir"/twig

echo '****' creazione symlinks
ln -s "$composer_folder" "$current_dir"/twig
ln -s "$npm_folder" "$current_dir"
ln -s "$_DEV_folder" "$current_dir"

echo '****' FINITO
