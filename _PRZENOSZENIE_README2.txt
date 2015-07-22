W odróżnieniu od _PRZENOSZENIE_README.txt, ten plik został stworzony wiele lat
później. Piszę go dnia 22 lipca 2015. I w następnym commicie go usunę, bo to
tylko notatka.

Przy migracji z SVN do Git nastąpiło "przepisanie historii". Zacytuję tutaj
email, który wysłałem na listę RT:

"""
Pojawiły się pierwsze problemy - o ile zgłoszone wcześniej przeze mnie
błędy w "GitHub Exporterze" zostały poprawione (dotyczyły eksportu
danych issue trackera), o tyle pojawił się inny problem, który
muszę obejść samodzielnie:

Niektórzy może jeszcze pamiętają - zanim postawiłem repozytorium na
Google Code, istniało jeszcze inne repozytorium SVN. Tamto
repozytorium SVN miało niestety niestandardowy format, tzn. nie
posiadało folderów "trunk", "branches" i "tags". Naprawiłem
repozytorium w momencie migracji do Google Code, ale stało się to
dopiero w rewizji 3745 (rok 2012).

Oznacza to, że rewizje 1-3745 (czyli historia z lat 2009-2012) są
niekompatybilne z Gitem. A co za tym idzie cała historia SVN nie jest
"standardowa" i przeniesienie jej wymaga trochę dodatkowej pracy
(GitHub exporter się na tym wywala).

1. Bardzo łatwo mogę przenieść historię od commita 4480 wzwyż (to jest
końcówka roku 2012). Wynika to z tego, że tę historię mam już u siebie
lokalnie, bo od paru lat korzystam z Gita do pracy z naszym SVN (da
się).

2. Wydaje mi się, że stosunkowo łatwo mogę odzyskać historię 3745-4480
(marzec-grudzień 2012). Będzie to wymagać jednak uruchomienia
długotrwałego skryptu, więc na pewno nie skończę dzisiaj.

3. Mogę próbować odzyskać commity 1-3745 (lata 2009-2012) poprzez
"przepisywanie historii", ale zastanawiam się czy warto? Co myślicie?
Czy tak stara historia kiedykolwiek będzie nam do czegoś potrzebna?
"""

A więc zająłem się punktem 3. Wszystkie commity, które widzisz PRZED dodaniem
tego pliku README powstały właśnie w latach 2009-2012. Ten jeden commit powstał
w roku 2015, ale kolejne (następującej PO tym commicie) powstały w latach
2012-2015. Zostawiam ten plik w historii, żeby było mniej więcej wiadomo co
się stało.

Niestety tego pliku nie będę mógł już nigdy edytować, więc jeśli interesuje Cię
co dokładnie dzieje się w okolicznych commitach, to skontaktuj się ze mną lub
wyszukaj starą konwersację w historii emaili (jeśli Ty jej nie masz, to być
może inni członkowie RT powinni mieć do niej dostęp).
