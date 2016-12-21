Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/xenial64"

  # Telnet port for the running game
  config.vm.network "forwarded_port", guest: 9000, host: 9000

  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    apt-get install -y php7.0 php7.0-sqlite3
  SHELL

end
