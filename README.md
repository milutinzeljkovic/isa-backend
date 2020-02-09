### Projekat ISA-PSW 2019/20 (BackEnd)
### Neophodno za pokretanje projekta:
  - Composer
  - PHP
  - Docker
  
### Preuzeti projekat sa linka sa komandom:
  git clone https://github.com/milutinzeljkovic/isa-backend

### Pokretanje:
   - Potrebno je pozicionirati se u src folder sa komandom: cd src
   - Instalirati potrebne dependency-e sa: composer install
   - Kreirati konfiguracioni fajl: cp .env.example .env
   - Kreirajte novu MySQL bazu i u env fajlu koji ste napravili unesite podatke kako biste se povezali sa bazom
   - Da biste bazu pokrenuli potrebno je uneti komandu: docker-compose up -d, dok za gašenje baze se koristi: docker-compose down
   - U okruženju u kojem ste otvorili terminal uneti komandu: php artisan migrate (morate biti i dalje u src-u jer je tu pozicioniran           artisan
   - Zatim se generišu ključevi za autorizaciju sa komandama: php artisan key: generate i nakon toga php artisan jwt:secret
   - Nakon što ste ovo sve odradili za pokretanje se koristi: php artisan serve
   
   ### TIM32 - Milutin Zeljković, Mihailo Stanarević, Ivan Činčurak
