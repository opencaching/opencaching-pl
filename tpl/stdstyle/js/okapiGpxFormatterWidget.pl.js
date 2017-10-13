(function($) {

    "use strict";

    $.okapiGpxFormatterWidget.extendTranslationStrings({
        downloadButtonLabel: "Pobierz GPX",
        cancelButtonLabel: "Anuluj",
        continueButtonLabel: "Dalej >",
        closeButtonLabel: "Zamknij",

        infoHeaderHTML: (
            "<p><a href='/okapi/'>OKAPI</a> potrafi generować przeróżne \"odmiany\" plików GPX. " +
            "Poniżej możesz spróbować samodzielnie przestawić niektóre spośród opcji, które OKAPI nam udostępnia. " +
            "Pamiętaj jednak, że niepoprawny wybór opcji może spowodować, że co poniektóre aplikacje mogą mieć " +
            "problemy z wyświetleniem pliku.</p>"
        ),
        numberOfCachesHeader: "Liczba skrzynek do pobrania:",

        paramLpcHeader: "Wpisy do logów:",
        paramLpc_0: "Nie dołączaj żadnych wpisów.",
        paramLpc_10: "Dołącz tylko 10 najświeższych wpisów.",
        paramLpc_mine: "Include only my logs.",  /* TODO: translate */
        paramLpc_all: "Dołącz wszystkie wpisy.",

        paramTrackablesHeader: "Czy dołączać informacje o Geokretach (w opisie skrzynki)?",
        paramTrackables_none: "Nie dołączaj.",
        paramTrackables_count: "Pokaż tylko informację o aktualnej ilości Geokretów w skrzynce.",
        paramTrackables_all: "Pokaż pełną listę Geokretów aktualnie znajdujących się w skrzynce.",

        paramAttrsHeader: "Atrybuty skrzynki:",
        paramAttrs_desctext: "Pokaż listę atrybutów bezpośrednio w opisie skrzynki.",
        paramAttrs_oxtags_HTML: "Wygeneruj element <code>ox:tag</code> dla wszystkich atrybutów (niektóre urządzenia mogą je rozpoznać).",
        paramAttrs_gcattrs_HTML: "Wygeneruj atrybuty kompatybilne z <i>geocaching.com</i>, o ile się da (a da się niestety tylko dla znikomej liczby atrybutów).",

        paramMyNotesHeader: "Czy (i w jaki sposób) osadzić w pliku osobiste notatki?",
        paramMyNotes_desctext: "Pokaż moje osobiste notatki bezpośrednio w opisie skrzynki.",
        paramMyNotes_gcpersonalnote_HTML: "Wygeneruj odpowiednie elementy <code>groundspeak:personal_note</code>.",

        paramLocationSourceHeader: "Które z dostępnych współrzędnych uznać jako \"główne\"?",
        paramLocationSource_default: "Oryginalne współrzędne z mapy (podane przez autora skrzynki).",
        paramLocationSource_usercoords: "Gdy tylko się da, użyj moich własnych współrzędnych (opcja może być przydatna np. w przypadku znalezionych już Quizów).",
        paramLocationSource_parking: "Współrzędne parkingu, o ile zostały podane (zostaną pobrane z listy dodatkowych punktów, zdefiniowanych przez autora skrzynki).",

        paramImagesHeader: "W jaki sposób dołączyć obrazki/zdjęcia?",
        paramImages_none: "Nie dołączaj.",
        paramImages_thumblinks: "Pokaż miniaturki z odnośnikami. Spoilery będą zakryte, podobnie jak na stronie WWW.",
        paramImages_nonspoilers: "Pokaż od razu duże wersje zdjęć, ale pomiń spoilery.",
        paramImages_all: "Pokaż od razu duże wersje wszystkich zdjęć, wliczając spoilery.",
        paramImages_oxall_HTML: "Nie dołączaj elementów <code>img</code> w opisie skrzynki. Zamiast tego wygeneruj elementy <code>ox:image</code> (dla urządzeń Garmin).",

        otherOptionsHeader: "Pozostałe opcje:",
        otherOptions_protection_areas: "Pokaż informacje o rezerwatach i innych obszarach chronionych (jeśli skrzynka się w takowym znajduje).",
        otherOptions_recommendations: "Pokaż informację o liczbie przyznanych rekomendacji (w opisie skrzynki).",
        otherOptions_alt_wpts: "Dołącz wszystkie dodatkowe punkty/waypointy, które autor przypisał do skrzynki.",
        otherOptions_mark_found: "Oznacz skrzynki, które już znalazłem (zostaną wygenerowane z symbolem otwartej skrzynki).",

        additionalDownloadsHeaderHTML: (
            "<p>Gdy pobieranych jest powyżej 500 skrzynek, plik GPX jest dzielony na " +
            "parę mniejszych plików. Każdy z nich musi być pobrany niezależnie:</p>"
        ),
    });

})(jQuery);
