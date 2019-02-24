#!/bin/sh

# set ambiente per progetti twig

read -e -p "Directory del progetto: " DEST_DIR
read -e -p "Directory del bundle (fac.): " BUNDLE_DIR
#DEST_DIR=$1

if [ "$DEST_DIR" == "" ]; then
  echo 'Destinazione mancante'
  exit 0
fi
if [ ! -d "$DEST_DIR" ]; then
  echo "Directory $DEST_DIR non esistente"
  exit 0
fi

#sudo chmod 775 "$DEST_DIR"

vendor_folder=~/Sites/vendor
node_modules_folder=~/Sites/node_modules
dev_utilities_folder=~/Sites/_dev-utilities
templates_dir="$dev_utilities_folder"/templates

rm -rf "$DEST_DIR"/vendor
rm -rf "$DEST_DIR"/_dev
rm -rf "$DEST_DIR"/_dev-utilities
rm -rf "$DEST_DIR"/node_modules
rm -rf "$DEST_DIR"/-public
rm -rf "$DEST_DIR"/-views

# creazione symlinks
ln -s "$vendor_folder" "$DEST_DIR"
ln -s "$node_modules_folder" "$DEST_DIR"
ln -s "$dev_utilities_folder" "$DEST_DIR"

# bundle dir

if [ "$BUNDLE_DIR" != "" ] && [ ! -d "$BUNDLE_DIR" ]; then
  echo "Directory $BUNDLE_DIR non esistente"
  exit 0
fi

if [ "$BUNDLE_DIR" == "" ]; then
  mkdir "$DEST_DIR"/__bundle__dir__
  BUNDLE_DIR="$DEST_DIR"/__bundle__dir__
fi

if [ ! -d "$BUNDLE_DIR"/Resources ]; then
  mkdir "$BUNDLE_DIR"/Resources
fi
if [ ! -d "$BUNDLE_DIR"/Resources/views ]; then
  mkdir "$BUNDLE_DIR"/Resources/views
fi

if [ ! -d "$BUNDLE_DIR"/Resources/public ]; then
  mkdir "$BUNDLE_DIR"/Resources/public
fi

ln -s "$BUNDLE_DIR"/Resources/views "$DEST_DIR"/-views
ln -s "$BUNDLE_DIR"/Resources/public "$DEST_DIR"/-public

# copia file di default
if [ ! -f "$DEST_DIR"/.htaccess ]; then
  cp "$templates_dir"/htaccess.txt "$DEST_DIR"/.htaccess
fi
if [ ! -f "$DEST_DIR"/index.html ]; then
  cp "$templates_dir"/index.html "$DEST_DIR"
fi
if [ ! -d "$DEST_DIR"/_TEST ]; then
  mkdir "$DEST_DIR"/_TEST
  mkdir "$DEST_DIR"/_TEST/_test
  cp "$templates_dir"/_local_config.TEMPLATE.incl.php "$DEST_DIR"/_TEST/_local_config.incl.php
fi

if [ ! -f "$BUNDLE_DIR"/.gitignore ]; then
  cp "$templates_dir"/gitignore.txt "$BUNDLE_DIR"/.gitignore
fi
if [ ! -f "$BUNDLE_DIR"/Resources/.editorconfig ]; then
  cp "$templates_dir"/editorconfig.txt "$BUNDLE_DIR"/Resources/.editorconfig
fi
if [ ! -f "$BUNDLE_DIR"/Resources/public/.eslintrc.json ]; then
  cp "$templates_dir"/eslintrc.json "$BUNDLE_DIR"/Resources/public/.eslintrc.json
fi
if [ ! -f "$BUNDLE_DIR"/Resources/public/package.json ]; then
  cp "$templates_dir"/std_package.json "$BUNDLE_DIR"/Resources/public/package.json
fi




echo '**** FINITO ****'
