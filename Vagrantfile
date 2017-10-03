# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure('2') do |config|
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://vagrantcloud.com/search.
  config.vm.box = 'ubuntu/xenial64'

  config.ssh.insert_key = true
  config.ssh.forward_agent = true

  defaultRepoName = 'twine-package-manager'
  repoName = ENV['TWINEPM_REPO_NAME'] || defaultRepoName

  config.vm.provider 'virtualbox' do |vb|
    vb.name = repoName
  end

  config.vm.define repoName

  # web
  config.vm.network 'forwarded_port', guest: 8000, host: 8000

  # client
  config.vm.network 'forwarded_port', guest: 8080, host: 8080

  defaultBranch = 'dev'
  branch = ENV['TWINEPM_BRANCH'] || defaultBranch

  defaultRepoSite = 'https://github.com'
  repoSite = ENV['TWINEPM_REPO_SITE'] || defaultRepoSite

  defaultRepoOwner = 'furkleindustries'
  repoOwner = ENV['TWINEPM_REPO_OWNER'] || defaultRepoOwner

  shellStr =
    'apt-get update && ' +
    'apt-get install -y python3 python-pip && ' +
    'pip install --upgrade pip && ' +
    'pip install aws-shell && ' +
    'cd /etc/ && ' +
    "TWINEPM_BRANCH=#{branch} && " +
    'export TWINEPM_BRANCH && ' +
    "echo \"\nTWINEPM_BRANCH=$TWINEPM_BRANCH\n" +
      "export TWINEPM_BRANCH\n\" >> /home/ubuntu/.bashrc && " +
    "TWINEPM_REPO_SITE=#{repoSite} && " +
    'export TWINEPM_REPO_SITE && ' +
    "echo \"TWINEPM_REPO_SITE=$TWINEPM_REPO_SITE\n" +
      "export TWINEPM_REPO_SITE\n\" >> /home/ubuntu/.bashrc && " +
    "TWINEPM_REPO_OWNER=#{repoOwner} && " +
    'export TWINEPM_REPO_OWNER && ' +
    "echo \"TWINEPM_REPO_OWNER=$TWINEPM_REPO_OWNER\n" +
      "export TWINEPM_REPO_OWNER\n\" " +
      '>> /home/ubuntu/.bashrc && ' +
    "TWINEPM_REPO_NAME=#{repoName} && " +
    'export TWINEPM_REPO_NAME && ' +
    "echo \"TWINEPM_REPO_NAME=$TWINEPM_REPO_NAME\n" +
      "export TWINEPM_REPO_NAME\n\" >> " +
      '/home/ubuntu/.bashrc && ' +
    'git clone -b $TWINEPM_BRANCH ' +
      "$TWINEPM_REPO_SITE/$TWINEPM_REPO_OWNER/$TWINEPM_REPO_NAME && " +
    'cd $TWINEPM_REPO_NAME && ' +
    './cloneSubrepositories && ' +
    'scripts/installHostDependencies && ' +
    'scripts/buildContainers --run && ' +
    'echo "Done provisioning VM."'

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: shellStr
end