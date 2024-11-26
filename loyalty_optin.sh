echo "WPLoyalty Optin Pack"
current_dir="$PWD"

react_block_folder_path=$current_dir"/blocks"
react_block_node_modules_path=$current_dir"/blocks/node_modules"
react_block_build_path="/blocks/build"

composer_run() {
  # shellcheck disable=SC2164
  cd "$current_dir"
  composer install --no-dev
  composer update --no-dev
  echo "Compress Done"
  echo  "WPLoyalty Optin Block NPM"
  cd "blocks";
  rm -r "$react_block_node_modules_path"
  rm -r "$react_block_build_path"
  # shellcheck disable=SC2164
  source ~/.nvm/nvm.sh
  nvm use 20
  npm i -q
  npm run build -q
  echo "WPLoyalty Optin Block Done"
  # shellcheck disable=SC2164
  cd $current_dir
}
update_ini_file(){
  cd $current_dir
  wp i18n make-pot . "i18n/languages/wp-loyalty-optin.pot" --slug="wp-loyalty-optin" --domain="wp-loyalty-optin" --include="wp-loyalty-optin.php",/App/,/blocks/ --headers='{"Last-Translator":"WPloyalty","Language-Team":"WPLoyalty"}' --allow-root
  cd $current_dir
  echo "Update ini done"
}
copy_folder() {
  cd $current_dir
  cd ..
  pack_folder=$PWD"/compressed_pack"
  compress_plugin_folder=$pack_folder"/wp-loyalty-optin"
  if [ -d "$pack_folder" ]; then
    rm -r "$pack_folder"
  fi
  mkdir "$pack_folder"
  mkdir "$compress_plugin_folder"
  mkdir "$compress_plugin_folder/blocks"
  move_dir=("App" "Assets" "i18n" "blocks/build" "vendor" "wp-loyalty-optin.php")
  # shellcheck disable=SC2068
  for dir in ${move_dir[@]}; do
    cp -r "$current_dir/$dir" "$compress_plugin_folder/$dir"
  done
  cd "$current_dir"
}

zip_folder() {
  cd "$current_dir"
  cd ..
  pack_compress_folder=$PWD"/compressed_pack"
  cd "$pack_compress_folder"
  pack_name="wp-loyalty-optin"
  zip_name="wp-loyalty-optin"
  rm "$zip_name".zip
  zip -r "$zip_name".zip $pack_name -q
  zip -d "$zip_name".zip __MACOSX/\*
  zip -d "$zip_name".zip \*/.DS_Store
}
echo "Composer Run:"
composer_run
echo "Update ini"
update_ini_file
echo "Copy Folder:"
copy_folder
echo "Zip Folder:"
zip_folder
echo "End"