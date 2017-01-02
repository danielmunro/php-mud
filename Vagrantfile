Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/xenial64"

  # Telnet port for the running game
  config.vm.network "forwarded_port", guest: 9000, host: 9000

  config.vm.provision "shell", inline: <<-SHELL
    export DEBIAN_FRONTEND=noninteractive
    add-apt-repository ppa:ondrej/php
    apt-get update
    apt-get install -q -y php7.1 php7.1-dom php7.1-curl php7.1-mbstring php7.1-mysqli mysql-server mysql-client
  SHELL

end
