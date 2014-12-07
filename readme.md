### Useful CLI Commands

Resote MySQL snapshot from GCS to Colossus.
```
gcloud sql instances import colossus gs://discodonniepresents.com/ddp_production.sql.gz --database="DiscoDonniePresents/www.discodonniepresents.com"
```

### Setting up Local Environment
Before running the following commands, please setup a new MySQL database calling it "edm_develop".
You will also need to have [gsutil](https://cloud.google.com/storage/docs/gsutil), [wp-cli](http://wp-cli.org/gsutil) and [direnv](https://github.com/zimbatm/direnv) installed:

Assuming your local site setup will be in ~/Sites/discodonniepresents.com using the **develop** branch:
```
cd ~/Sites/discodonniepresents.com
git clone git@github.com:DiscoDonniePresents/www.discodonniepresents.com.git .
git checkout develop
echo "export WP_ENV=develop" >> .envrc
echo "export DB_NAME=edm" >> .envrc
echo "export DB_USER=edm" >> .envrc
echo "export DB_PASSWORD=your-mysql-password" >> .envrc
echo "export DB_HOST=127.0.0.1" >> .envrc
echo "export WP_ELASTIC_SECRET_KEY=jqnp-krmw-nmap-idpk:julw-urbp-vzst-jwwv" >> .envrc
echo "export WP_ELASTIC_PUBLIC_KEY=jqnp-krmw-nmap-idpk-ooau-bkfm-bghf-jatg" >> .envrc
echo "export WP_ELASTIC_SERVICE_URL=api.discodonniepresents-com.drop.ud-dev.com" >> .envrc
echo "export WP_ELASTIC_SERVICE_INDEX=jqnp-krmw-nmap-idpk" >> .envrc
make subtreePull
make snapshotImport
wp cloud sites
```

The very last command is to help you determine if wp-cli can connect to DB, meaning everything is setup good.

Now we will need to configure Apache. First create a "discodonniepresents.com" host then set the following environment variables:

```
curl -X PURGE discodonniepresents.com
curl -X PURGE dayafter.com
curl -X PURGE umesouthpadre.com
```

### Subtrees
Add "subtree helpers" to your bash profile. (https://gist.github.com/andypotanin/e54a7322da3fa33ada7e) to simplify subtree adding/pulling/pushing:

```
makeSubtree UsabilityDynamics/wp-veneer           vendor/plugins/wp-veneer
makeSubtree UsabilityDynamics/wp-cluster          vendor/plugins/wp-cluster
makeSubtree UsabilityDynamics/wp-elastic          vendor/plugins/wp-elastic
makeSubtree UsabilityDynamics/wp-network          vendor/plugins/wp-network
makeSubtree UsabilityDynamics/wp-github-updater   vendor/plugins/wp-network
makeSubtree UsabilityDynamics/wp-splash           vendor/themes/wp-splash
makeSubtree wpCloud/wp-vertical-edm               vendor/plugins/wp-vertical-edm
makeSubtree wpCloud/wp-event-post-type            vendor/plugins/wp-event-post-type
```

```
makeSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v2.0       v2.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-v1.0    v1.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-v2.0    v2.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-v1.0   v1.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-v2.0   v2.0
makeSubtree DiscoDonniePresents/wp-spectacle-chmf  vendor/themes/wp-spectacle-chmf
makeSubtree DiscoDonniePresents/wp-spectacle-mbp  vendor/themes/wp-spectacle-mbp
makeSubtree DiscoDonniePresents/wp-spectacle-fbt  vendor/themes/wp-spectacle-fbt
makeSubtree DiscoDonniePresents/wp-spectacle-isladelsol  vendor/themes/wp-spectacle-isladelsol
```

Pull subtrees.
```
pullSubtree UsabilityDynamics/wp-cluster            vendor/plugins/wp-cluster
pullSubtree UsabilityDynamics/wp-veneer             vendor/plugins/wp-veneer
pullSubtree UsabilityDynamics/wp-crm                vendor/plugins/wp-crm
pullSubtree DiscoDonniePresents/wp-spectacle        vendor/themes/wp-spectacle-v1.0   v1.0
pullSubtree DiscoDonniePresents/wp-spectacle        vendor/themes/wp-spectacle-v2.0   v2.0
pullSubtree DiscoDonniePresents/wp-festival         vendor/themes/wp-festival-v2.0    v2.0
pullSubtree DiscoDonniePresents/wp-spectacle-chmf   vendor/themes/wp-spectacle-chmf
pullSubtree DiscoDonniePresents/wp-spectacle-mbp    vendor/themes/wp-spectacle-mbp
pullSubtree DiscoDonniePresents/wp-spectacle-fbt    vendor/themes/wp-spectacle-fbt
pullSubtree DiscoDonniePresents/wp-spectacle-isladelsol  vendor/themes/wp-spectacle-isladelsol
```

Update subtrees.
```
pushSubtree UsabilityDynamics/wp-cluster            vendor/plugins/wp-cluster
pushSubtree UsabilityDynamics/wp-veneer             vendor/plugins/wp-veneer
pushSubtree UsabilityDynamics/wp-crm                vendor/plugins/wp-crm
pushSubtree DiscoDonniePresents/wp-spectacle-mbp    vendor/themes/wp-spectacle-mbp
pushSubtree DiscoDonniePresents/wp-disco            vendor/themes/wp-disco-v2.0 v2.0
pushSubtree DiscoDonniePresents/wp-spectacle        vendor/themes/wp-spectacle-v2.0   v2.0
pushSubtree DiscoDonniePresents/wp-festival         vendor/themes/wp-festival-v2.0    v2.0
```

Show installed libs:
```
composer show --installed --path
```

Show versions of libs:
```
composer show --self
```

### Staging

* Now, when commiting to the 'develop' branch, your changes will be automatically deployed to the following domain name:
  {domain}.drop.ud-dev.com, i.e. dayafter.com becomes "dayafter-com.drop.ud-dev.com"
* In addition, we have a database backup done daily, that can be restored by including the following text in your commit message:
  [drop refreshdb]

### MySQL Backup and Restore
Create Backup, either run "make snapshot" to create an automatic snapshot that uses branch name, or create a manually DB backup:
```
wp transient delete-all && wp cache flush
wp db export edm_production.sql
tar cvzf edm_production.sql.tgz edm_production.sql
s3cmd put --no-check-md5 --reduced-redundancy edm_production.sql.tgz s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
mv  edm_production.sql**
```

To fetch backup locally and import it:
```
s3cmd get s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
tar xvf edm_production.sql.tgz
wp db import edm_production.sql
```


### Media Sync

#### Standard Sites' Media
```
gsutil -m rsync -d  /var/storage/beachblanketfestival.com/media/             gs://media.beachblanketfestival.com/
gsutil -m rsync -d  /var/storage/cominghomemusicfestival.com/media/          gs://media.cominghomemusicfestival.com/
gsutil -m rsync -d  /var/storage/dayafter.com/media/                         gs://media.dayafter.com/
gsutil -m rsync -d  /var/storage/discodonniepresents.com/media/              gs://media.discodonniepresents.com/
gsutil -m rsync -d  /var/storage/freaksbeatstreats.com/media/                gs://media.freaksbeatstreats.com/
gsutil -m rsync -d  /var/storage/gifttampa.com/media/                        gs://media.gifttampa.com/
gsutil -m rsync -d  /var/storage/isladelsolfest.com/media/                   gs://media.isladelsolfest.com/
gsutil -m rsync -d  /var/storage/monsterblockparty.com/media/                gs://media.monsterblockparty.com/
gsutil -m rsync -d  /var/storage/smftampa.com/media/                         gs://media.smftampa.com/
gsutil -m rsync -d  /var/storage/somethingwicked.com/media/                  gs://media.somethingwicked.com/
gsutil -m rsync -d  /var/storage/suncitymusicfestival.com/media/             gs://media.suncitymusicfestival.com/
gsutil -m rsync -d  /var/storage/winterfantasyrgv.com/media/                 gs://media.winterfantasyrgv.com/
gsutil -m rsync -d  /var/storage/umesouthpadre.com/media/                    gs://media.umesouthpadre.com/
```

#### Archived Sites' Media
```
gsutil -m rsync -d  /var/storage/hififest.com/media/                         gs://ddpsdixyeejhwkgg.wpcloud.zone/media/hififest.com
gsutil -m rsync -d  /var/storage/bassodyssey.com/media/                      gs://ddpsdixyeejhwkgg.wpcloud.zone/media/bassodyssey.com
gsutil -m rsync -d  /var/storage/wildwood.beachblanketfestival.com/media/    gs://ddpsdixyeejhwkgg.wpcloud.zone/media/wildwood.beachblanketfestival.com
gsutil -m rsync -d  /var/storage/galveston.beachblanketfestival.com/media/   gs://ddpsdixyeejhwkgg.wpcloud.zone/media/galveston.beachblanketfestival.com
gsutil -m rsync -d  /var/storage/mexico.lightsallnight.com/media/            gs://ddpsdixyeejhwkgg.wpcloud.zone/media/mexico.lightsallnight.com
gsutil -m rsync -d  /var/storage/2014.dayafter.com/media/                    gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2014.dayafter.com
gsutil -m rsync -d  /var/storage/2013.monsterblockparty.com/media/           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2013.monsterblockparty.com
gsutil -m rsync -d  /var/storage/2013.freaksbeatstreats.com/media/           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2013.freaksbeatstreats.com
gsutil -m rsync -d  /var/storage/sugarsociety.com/media/                     gs://ddpsdixyeejhwkgg.wpcloud.zone/media/sugarsociety.com
gsutil -m rsync -d  /var/storage/gxgmag.com/media/                           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/gxgmag.com
```

#### Archived Sites' Media (Broken)
```
gsutil -m rsync -rd  /var/storage/2015.umesouthpadre.com/media/               gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2015.umesouthpadre.com
```

#### Media Permissions
```
gsutil -m setacl -R -a public-read gs://media.beachblanketfestival.com
gsutil -m setacl -R -a public-read gs://media.cominghomemusicfestival.com
gsutil -m setacl -R -a public-read gs://media.dayafter.com
gsutil -m setacl -R -a public-read gs://media.discodonniepresents.com
gsutil -m setacl -R -a public-read gs://media.freaksbeatstreats.com
gsutil -m setacl -R -a public-read gs://media.gifttampa.com
gsutil -m setacl -R -a public-read gs://media.isladelsolfest.com
gsutil -m setacl -R -a public-read gs://media.monsterblockparty.com
gsutil -m setacl -R -a public-read gs://media.smftampa.com
gsutil -m setacl -R -a public-read gs://media.somethingwicked.com
gsutil -m setacl -R -a public-read gs://media.suncitymusicfestival.com
gsutil -m setacl -R -a public-read gs://media.winterfantasyrgv.com
gsutil -m setacl -R -a public-read gs://media.umesouthpadre.com
gsutil -m setacl -R -a public-read gs://ddpsdixyeejhwkgg.wpcloud.zone/media
```

### Docker Run

```
docker run -itd \
  --name=www.discodonniepresents.com \
  --hostname=www.discodonniepresents.com \
  --volume=/home/core/.ssh:/home/core/.ssh \
  --volume=/opt/storage/DiscoDonniePresents/www.discodonniepresents.com:/var/storage \
  --volume=/opt/sources/DiscoDonniePresents/www.discodonniepresents.com:/var/www \
  --publish=80:80 \
  --env=WP_ENV=production \
  --env=NODE_ENV=production \
  --env=WPC_HOST=fallujah.wpcloud.io \
  --env=WPC_STACK=gce \
  discodonniepresents/www.discodonniepresents.com:2.1.5
```

```
docker exec -it www.discodonniepresents.com /bin/bash
```

```
docker build --tag=discodonniepresents/www.discodonniepresents.com:2.1.5 /opt/sources/DiscoDonniePresents/www.discodonniepresents.com
```