site:
	ansible-playbook -i hosts.yml site.yml

site:
	ansible-playbook -i hosts.yml upgrade.yml

authorize:
	ansible-playbook -i hosts.yml authorize.yml

docker-login:
	ansible-playbook -i hosts.yml docker-login.yml

generate-deploy-key:
	ssh-keygen -q -t ed25519 -N '' -f files/deploy_ed25519

authorize-deploy:
	ansible-playbook -i hosts.yml authorize-deploy.yml
