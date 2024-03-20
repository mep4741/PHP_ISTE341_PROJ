<?php

class PageFormat {

    private static function getHeader($pageTitle = "Untitled", $fileFirstPath = ".") {

        $linkList = [
            "Events" => "<a href=\"$fileFirstPath/home.php\">Events</a>",
            "Registrations" => "<a href=\"$fileFirstPath/registrations.php\">Registrations</a>",
            "Admin" => "<a href=\"$fileFirstPath/admin.php\">Admin</a>",
        ];

        // add the active class to the current page if it exists in the navigation
        if (isset($linkList[$pageTitle])) {
            $shortedLink = substr($linkList[$pageTitle],2);
            $linkList[$pageTitle] = "<a class=\"active\"".$shortedLink;
        }

        // get the role of the current user
        $role = CurrentUser::getCurrentUser($fileFirstPath)->getRole();
        if ($role == '3') {
            $linkList = array_slice($linkList, 0, 2);
        }

        $linkString = "";
        foreach ($linkList as $link) {
            $linkString = $linkString."$link<br>";
        }

        $header = <<< END

        <nav>
            <a href="#" class="nav-icon"><span class="material-symbols-outlined">
            event
            </span></a>
            <div class="links">
                $linkString
                <form class="logout" method="POST">
                    <input type="submit" name="Logout" value="Logout" >
                </form>
            </div>
        </nav>
        
        END;

        return $header;
    }

    private static function getFooter() {
        $footer = <<< END

            <footer>
                <p>Made by <a href="https://maija.xyz/" target="_blank">Maija Philip</a> &copy; 2023</p>
            </footer>

        END;

        return $footer;
    }

    static function getDocumentHeader($pageTitle = "Untitled", $fileFirstPath = ".", $isLogin = false) {

        if(isset($_POST['Logout'])) { 
            // If they pressed logout, log them out and send them to login page
            require_once "$fileFirstPath/utils/CurrentUser.class.php";
            CurrentUser::logout();
            header("Location: $fileFirstPath/login.php");
            exit;
        } 
        
        $header = "<nav><a href=\"#\" class=\"nav-icon\"><span class=\"material-symbols-outlined\">event</span></a><div class=\"links\"><a href=\"#\" class=\"nav-icon\">Event Manager</a></div></nav>";
        if (!$isLogin){
            $header = self::getHeader($pageTitle, $fileFirstPath);
        }
        
        $documentHeader = <<< END

        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" >
            <!-- My Stylesheet -->
            <link rel="stylesheet" type="text/css" href="$fileFirstPath/utils/Styles.css">
            <!-- Event Icon -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" >
            <!-- Add Icon -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" >
            <!-- Edit Icon -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" >
            <!-- Person Icon -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" >


            <title>$pageTitle | Project 1</title>
        </head>
        <body>
            $header
            <main>

        END;

        return $documentHeader;
    }

    static function getDocumentFooter() {
        $footer = self::getFooter();

        $documentFooter = <<< END

        </main>
            $footer

        </body>
        </html>

        END;

        return $documentFooter;
    }

    static function getAddFAB() {
        $addButton = <<< END

            <a href="editpages/editEvent.php">
                <button class="fab">
                    <p><span class="material-symbols-outlined">
                        add
                    </span></p>
                </button>
            </a>

        END;

        return $addButton;
    }
}