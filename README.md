### Projekat ISA-PSW 2019/20 (BackEnd)
### Neophodno za pokretanje projekta:
  - Composer
  - PHP
  - Docker ili Mysql baza
  
### Preuzeti projekat sa linka sa komandom:
  git clone https://github.com/milutinzeljkovic/isa-backend

### Pokretanje:
   - Ukoliko radite preko dockera iz glavog foldera pokrenuti komandu docker-compose up -d
   - Ukoliko radite preko mysql pokrenite komande:
   - mysql -u root -e 'CREATE DATABASE isa;'
   - mysql -u root -e "CREATE USER 'user'@'localhost' IDENTIFIED BY 'user';"
   - mysql -u root -e "GRANT ALL ON isa.* TO 'user'@'localhost';"
   - Potrebno je pozicionirati se u src folder sa komandom: cd src
   - Instalirati potrebne dependency-e sa: composer install
   - Kreirati konfiguracioni fajl: cp .env.example .env
   - Podesiti kredencijale u env fajlu
   - U okruženju u kojem ste otvorili terminal uneti komandu: php artisan migrate (morate biti i dalje u src-u jer je tu            pozicioniran artisan
   - Zatim se generišu ključevi za autorizaciju sa komandama: php artisan key: generate i nakon toga php artisan jwt:secret
   - Nakon što ste ovo sve odradili za pokretanje se koristi: php artisan serve

### link 
https://isa-front-test.herokuapp.com/
 
   
   ### TIM32 - Milutin Zeljković, Mihailo Stanarević, Ivan Činčurak
