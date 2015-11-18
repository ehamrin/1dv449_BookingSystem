# 1dv449_BookingSystem

##Finns det några etiska aspekter vid webbskrapning. Kan du hitta något rättsfall?
Ja, webbskrapning kan tyvärr användas för kartläggning av t.ex. politiskt läggning m.m. En webbskrapa kan även användas för att samla information som kan skada en person/företag genom att använda datan för att konkurrera men denne.
https://www.techdirt.com/articles/20090605/2228205147.shtml

##Finns det några riktlinjer för utvecklare att tänka på om man vill vara "en god skrapare" mot serverägarna?
Ja, är det en omfattande skrapning så meddela i förväg. Man bör följa robots.txt samt kolla efter ev. "User Agreements". Det kan även vara snällt att lämna kontaktuppgifter i t.ex. User Agent-headern 

##Begränsningar i din lösning- vad är generellt och vad är inte generellt i din kod?
WebScraper klassen har hållts så generell som möjligt och är het enkelt en serviceklass för cURL och XPath. De övriga delarna bör kunna fungera på en sida som har liknande upplägg, men AJAX-anropet på biografen är hårdkodat och likaså veckodagarnas textrepresentation.
Jag har försökt att hålla det mesta som inte är dynamisk i en singletonklass MovieDateScraper.

##Vad kan robots.txt spela för roll?
Den kan användas som riktlinje för vad skrapan bör/inte bör kolla för sidor. Detta är dock ingenting som per automatik ger tillåtelse att det är okej att kartlägga information enligt en del ägare, se denna artikel om facebook: http://petewarden.com/2010/04/05/how-i-got-sued-by-facebook/
