## Installation Instructions

For the guide we are assuming you are on an Ubuntu machine. The instructions are basically the same for all linux distros other than installing packages from the official repositories.

```bash
# 1. Create a system folder where everything will reside
sudo mkdir /var/lib/chaalaa

# 2. Create a user group for the application
sudo groupadd chaalaa

# 3. Change the group of the system folder
sudo chown root:chaalaa /var/lib/chaalaa

# 4. Allow global access to the group for the system folder
sudo chmod 0775 /var/lib/chaalaa

# 5. Add git-shell to the available shells
echo $(which git-shell) | sudo tee -a /etc/shells > /dev/null

# 6. Create a user for git access to the server through SSH
sudo adduser --home /var/lib/chaalaa/git --shell $(which git-shell) git

# 7. Add the user to the `chaalaa` group
sudo usermod -a -G chaalaa git

# 8. Add PPA for PHP packages
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# 9. Install the necessary packages
sudo apt install php8.0-cli php8.0-bcmath php8.0-mcrypt php8.0-mysqli php8.0-opcache php8.0-redis php8.0-swoole php8.0-zip

# 10. Install composer from the official website
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
sudo chmod +x /usr/local/bin/composer

# 11. Install supervisor
sudo apt install supervisor

# 12. Install docker (https://docs.docker.com/engine/install/ubuntu/)
sudo apt remove docker docker-engine docker.io containerd runc
sudo apt install apt-transport-https ca-certificates curl gnupg lsb-release
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io

# 13. Log into the shell as git
sudo su --shell /usr/bin/bash git

# 14. Pull the latest version from GitHub
## If it's a fresh installation
cd /var/lib/chaalaa
git clone https://github.com/chaalaa/chaalaa.git app

## If it's an upgrade
cd /var/lib/chaalaa/app
git pull origin master

```
