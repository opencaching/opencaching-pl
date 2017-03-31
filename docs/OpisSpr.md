**Opis funkcjonalności: Sprawności**

1. Wstęp
2. Opis kolejnych stron - funkcjonalność z perspektywy użytkownika
3. Opis Silnika Sprawności – funkcjonalność z perspektywy programisty

**1. Wstęp**
- Zamiast medali, zaproponowałem pojęcie Sprawności

(medale wydały mi się zbyt napuszone, sprawności zaś kojarzą się z harcerstwem, więc moim zdaniem są bliższe naszej zabawie)

- Poniżej, często używane jest pojęcie Silnika Sprawności (w skrócie silnik). Pod tym pojęciem, rozumiem rozwiązanie programistyczne mające zapewnić obsługę nowej funkcjonalności: Sprawności. Opis możliwości silnika, znajdziecie poniżej w rozdziale 3.

- celem powstania Sprawności było danie kolejnego impulsu naszej zabawie do rozwoju poprzez proponowanie kolejnych wyzwań użytkownikom. Wyzwań związanych ze zdobywaniem kolejnych sprawności czy też pięciem się po szczeblach kolejnych poziomów poszczególnych sprawności. Dla wzmocnienia efektu wprowadzony został też element rywalizacji, w postaci tabeli znajdującej się na stronie każdej sprawności, w której użytkownik może sprawdzić jak radzą sobie inni użytkownicy z daną sprawnością.

**2. Opis kolejnych stron - funkcjonalność z perspektywy użytkownika**

**2.1 Strona: „Moje Statystyki”**

- na stronie: "Moje statystyki" pojawiła się nowa sekcja: Sprawności. W niej znajdują się miniaturki zdobytych przez użytkownika sprawności.

- tuż pod miniaturkami znajduje się link: **\[Pokaż szczegóły zdobytych sprawności ...\]** prowadzący do strony, z dużymi ikonami sprawności, z podziałem na kategorie

- po najechaniu myszką na miniaturę, na dymku pojawia się informacja na temat danej sprawności

- miniaturki otoczone są obwódką, jej kolor grubość oraz postęp zależy od tego na jakim etapie znajduje się zdobywanie danej sprawność

- miniaturki wyświetlają się w stałej kolejności

- jeżeli użytkownik nie rozpoczął jeszcze zdobywanie danej sprawności, jej miniaturka nie będzie się wyświetlała

- miniaturki mogą wyświetlać się w szarościach. Oznacza to, że zdobywca jest na pierwszym progu

**2.2 Strona: Szczegóły Zdobytych Sprawności**

- na stronie znajdują się 3 kategorie. Użytkownikowi wyświetlają się jedynie te kategorie do których należy przynajmniej jedna zdobyta przez użytkownika sprawność. Reszta kategorii pomimo, że jest zdefiniowana, nie zostanie wyświetlona.

- kolejność kategorii jest stała

- nad ikonami sprawności znajduje się informacje o: poziomie (więcej o nazwach poziomów w sekcji silnik) i „postępie/ile wynosi próg następnego poziomu”

- pod ikoną jest nazwa sprawności oraz jaki jest następny cel

- ikony sprawności otoczone są „obwódką – progresem” w odpowiednim kolorze i o odpowiedniej grubości (im wyższy poziom tym obwódka grubsza)
- po kliknięciu na nazwę sprawności, wchodzimy na stronę danej sprawności

**2.3 Strona: Szczegóły Zdobytych Sprawności**

- strona składa się z trzech części

- w pierwszej części znajdują się: ikona oraz podstawowe informacje dotyczące sprawności

- w drugiej części znajduje się miejsce na informację na temat sprawności (np. zasady zdobywania, jeżeli zaś sprawność związana jest z regionem, może się tam znaleźć także krótka informacja promująca dany region). Tutaj też mogą się znaleźć dane wyciągnięte z bazy danych. W przypadku sprawności wojewódzkich, umieściłem: liczbę aktywnych skrzynek, topowe skrzynki oraz skrzynki tygodnia

- w trzecią część przewidziałem na statystyki związane z daną sprawności. Znajdują się tam dwie tabele.
- po lewej stronie jest tabela z poszczególnymi poziomami, wraz z informacją przez ile osób dany poziom został już zdobyty (Licznik zdobyć) wraz z ostatnią datą zdobycia. Poziom na którym znajduje się użytkownik, został wyróżniony kolorem.

- po kliknięciu na dany poziom, po prawej stronie w tabeli pojawi się tabela z użytkownikami którzy zdobyli dany poziom; pozycja użytkownika została wyróżniona kolorem

**3. Silnik Sprawności **

- kolejne kategorie i sprawności konfiguruje się z poziomu bazy. Aby dodać nową sprawność (czy też kategorię) nie trzeba modyfikować kodu.
- sprawności podzielone są na kategorie. Może być dowolna ilość kategorii. Ich nazwy można zmieniać w dowolnym momencie
- w ramach danej kategorii można zdefiniować dowolną liczbę sprawności. Ich parametry można zmieniać w dowolnym momencie
- do sprawności należy przypisać grafikę (ikonę), tak aby miała swoją reprezentację graficzną
- każda ze sprawności może mieć dowolną liczbę poziomów, każdy z poziomów może mieć własną nazwę. Nazwę poziomu można zmienić w dowolnym momencie.
- każdemu z poziomów definiuje się próg, po którym następuje przeskoczenie na następny poziom
- każdy z poziomów może mieć własną grafikę. Jeżeli tak jest, to wtedy ta grafika jest wyświetlana na danym poziomie, zamiast grafiki przypisanej do sprawności.

- każdy z poziomów ma własny kolor w kontekście danej sprawności. Obecnie zostało zaproponowanych 10 kolorów. Jak wyglądają, można między innymi zobaczyć w tabeli poziomów (po lewej na stronie „Sprawność”). Ich liczba i wygląd może ulec jeszcze zmianie.
- tak jak wspomniałem liczba poziomów może być różna dla każdej ze sprawności. Mogą się pojawić sprawności z 3 poziomami, a mogą i takie które będą miały ich przykładowo 20. Silnik zadba o to aby, odpowiednio zarządzać kolorami w ten sposób aby ich kolejność zawsze była taka sama, bez względu na liczbę poziomów (w przypadku 20 poziomów, siłą rzeczy kolory mogą się powtarzać dla sąsiednich poziomów). Kolor ma być jedną z informacji dla użytkownika na jakim się poziomie się znajduje dana sprawność

- wokół ikon sprawności rysowane są obwódki postępu. Im bliżej użytkownik jest przeskoczenia na następny poziom, tym bardziej obwódka się zamyka.

- kolor oraz grubość obwódki świadczą o tym na jakim poziomie znajduje się dana sprawność (im grubsza obwódka tym wyższy poziom)
- wraz ze zdobyciem następnego poziomu, odkładana jest informacja ile wynosi następny próg bieżącego poziomu. Oznacza to, że nawet jeśli zmienimy wysokość progu w definicji sprawności to nie przekłada się to na na próg widoczny przez użytkownika. Innymi słowy, jeżeli próg przeskoczenia na 6 poziom wynosi 320, a my zmieniamy go na 400, to użytkownik pomimo tego nadal dąży do 320 (tzw problem króliczka, którego już prawie mamy, a on nam ucieka – to może nieźle sfrustrować użytkownika)
