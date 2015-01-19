Reflektion - Labb 2
======================

## Del 1 - Säkerhetsproblem

###Kommer åt message sidan direkt
-'Inloggad' direkt utan att skriva in användaruppgifter.  
-Vem som helst kan läsa ens meddelanden / alla kommer till samma sida? 

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*  
Tog bort index.html då koden för inloggning kör i index.php, och index.html var ändå överflödig.

###Kommer åt message-sidan utan att vara inloggad
-Genom att skriva in exakt url ex: http://localhost/Labb2/mess.php     
-Samma som punkten ovan.

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*  
Har byggt om strukturen i applikationen till MVC och kunde därmed ta bort mess.php filen och man kan inte 
komma åt den html som krävs för att visa message-sidan om man inte är inloggad.
	
###Lösenorden lagrat i klartext i databasen
-Lösenord som inte är hashade är väldigt lätta att hämta ut och man kan ex. 
 	använda lösenorden genom att testa om användaren lösenordet tillhör har samma lösenord till andra applikation ex. facebook.  
Då kan hackaren utge sig för att vara den personen på fb och lura dens annhöriga / vänner på exempelvis pengar. 

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*
-För att försvåra en dictionary attack eller brutforce attack har jag hashat lösenorden genom att först generera ett salt (som alltid är varierat) 
och sedan hashat lösenordet med saltet.  Det unika saltet är sparat i databasen för användaren och hämtas ut för att kunna jämföra lösenordet i databasen
med det inskrivna lösenordet från inputfältet.  
kodexempel: $salt = substr(str_shuffle(MD5(microtime())), 0, 10) . substr(str_shuffle(MD5(microtime())), 0, 5). substr(str_shuffle(MD5(microtime())), 0, 7);
$password = hash(SHA256, $salt . "admin"); 
 	
###Inputfält inte skyddat mot skadlig input (sql-injections)
-Man kan skriva in farliga tecken i inputfälten. 
-Kan utnyttjas genom att skriva in kod som kan förstöra databasen (ex drop tables eller tom drop database) 

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*
Jag har skapat en funtion som heter makeSafe, där jag skickar in username och password och tar bort alla farliga tecken som ex. backslashes och html-taggar	och tagit bort alla blanksteg!   (räcker det?)

###Cross site request forgery
- En annan sida kan via bilder eller formulär sätta adressen till min adress och skriva in egna actions, vilket kan göra mycket skada.  

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*
Jag har genererat ett token som sparas i en session, som jämförs med ett dolt fält med token nyckeln och om de två inte stämmer överrens så går inte olika saker att genomföra.
Ex. i fallen jag lyckades fixa, så går det inte att logga in eller ut utan token. 
Däremot lyckades jag inte ritkigt få till det vid add message, som kanske kunde varit bra. 

###Session hijacking
- Efterson som koden var slarvigt gjord fanns det inget direkt skydd emot att man kunde sno sessionsid med exempelvis javascript. 

####*Hur du har åtgärdat säkerhetshålet i applikationskoden?*
Jag sätter ett unikt id som är genererat beroende på vad användaren har för user agent och remote adress 
public function setUniqueID() {
      return sha1($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
    }
och jämför den sedan med den user agent och remote adress som personen försöker logga in från. 
Sedan flyttade jag httponly= true och satte som sista argument i session_set_cookie_params för att förhindra att sessions id kan kommas åt via javascript. 
session_set_cookie_params(3600, $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 

## Del 2 - Optimering

###Minimera javascript
-Har tagit bort dublicerade script, såsom att bootstrap.js och script.js vad identiska. 
-Har använt mig av "http://www.jsmini.com/" en javascript minify tool. 

####*Reflektion testfall*
ex. Messageboard.js tog innan 44.7ms (contend download) och efter minifiering 30.2ms

###Flyttat script-taggar
- Jag har flyttat alla scriptfiler i botten på sidorna precis över bodyn och css i head
- Man bör läsa in all css först i head (kap 5) då script blockerar all progressive rendering medans det körs, 
därför vill man placera scriptet sist  (kapitel 6 p.49)

####*Reflektion testfall*
Denna var inte så lätt att testa iom jag byggde om applikationen såpas mycket i strukturen.

###Tagit bort kodduplicering. 
- Jag har tagit bort kodduplicering då ex. script.js innehöll samma som bootsrap.js och css fanns dubbelt skrivet i olika filer så jag samlade det 
till en och samma fil. (Kapitel 12)


###Optimerat bilder
- Jag har minskat ex. logo från 40kb till 16kb (gjort samma typ av optimering för övriga bilder)
- Ju större en bild är desto längre tid tar det att ladda den, därför är det bra att optimera den så den blir så liten som möjligt men att den 
fortfarande ser bra ut. 

####*Reflektion testfall*
Exempelvis för logo som innan tog 89 ms, tar nu 160ms.....vilket förmodligen är för att den går igenom massa kod (för att verifiera användaren)  etc?  
men själva content download är ändå 10ms mer än vad den var när den va dubbelt så stor? clock tog 42ms innan och tar nu 37ms vilket är en förbättring om än väldigt liten.


## Del 3 - Long Polling
Jag har utgått från det här exemplet:
http://portal.bluejack.binus.ac.id/tutorials/webchatapplicationusinglong-pollingtechnologywithphpandajax 

Nu ligger servern och vilar (dock max ca 30sek) tills den har ny information att skicka ut till klienten.  
Istället för at klienten anropar och frågar servern hela tiden, som jag hade gjort innan med Ajax-polling. 



