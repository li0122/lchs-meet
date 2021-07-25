<!DOCTYPE html>
<html>
    
    <head>
        <title>
            LCHS MEET - Build By li0122/xig1517
        </title>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1, user-scalable=no"
        name="viewport">
        <link href="../assets/css/main.css" rel="stylesheet">
        <noscript>
            <link href="../assets/css/noscript.css" rel="stylesheet">
        </noscript>
    </head>
    
    <body class="is-preload">
        <!-- Wrapper -->
        <div id="wrapper">
            <!-- Header -->
            <header class="alt" id="header">
                <a class="logo" href="index.php">
                    <strong>
                        LCHS MEET
                    </strong>
                    <span>
                        by li0122/xig1517
                    </span>
                </a>
                <nav>
                    <a href="#menu">
                        Menu
                    </a>
                </nav>
            </header>
            <!-- Menu -->
            <nav id="menu">
                <ul class="links">
                    <li>
                        <a href="#">
                            Build
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            By
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            王瓅
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            朱晉岑
                        </a>
                    </li>
                </ul>
                <ul class="actions stacked">
                    <li>
                        <a class="button primary fit" href="#">
                            LCHS MEET
                        </a>
                    </li>
                    <li>
                        <a class="button fit" href="../login/action.logout.php">
                            LOGOUT
                        </a>
                    </li>
                    <li>
                        <a class="button fit" href="../login/action.logout.php">
                            更改密碼
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- Banner -->
            <section class="major" id="banner">
                <div class="inner">
                    <header class="major">
                        <h2>
                            教師確認簽到表
                        </h2>
                        <a class="button primary fit" href="index.php">
                            返回主頁
                        </a>
                    </header>
                </div>
            </section>
            <section id="one">
                <div class="inner">
                    <table class="alt">
                        <thead style="font-weight:bold;">
                            <tr>
                                <th align="center">
                                    姓名
                                </th>
                                <th align="center">
                                    座號
                                </th>
                                <th align="center">
                                    簽到
                                </th>
                                <th align="center">
                                    簽退
                                </th>
                            </tr>
                        </thead>
                        <tbody id="showData">
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <!-- Scripts -->
        <script src="../assets/js/jquery.min.js">
        </script>
        <script src="../assets/js/jquery.scrolly.min.js">
        </script>
        <script src="../assets/js/jquery.scrollex.min.js">
        </script>
        <script src="../assets/js/browser.min.js">
        </script>
        <script src="../assets/js/breakpoints.min.js">
        </script>
        <script src="../assets/js/util.js">
        </script>
        <script src="../assets/js/main.js">
        </script>
        <script>
            var param = getUrlParam('date');

            function getUrlParam(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
                return null;
            }

            function loadDatas() {
                $("#showData").load('action.getDatas.php', {
                    'date': param
                });
                setTimeout(loadDatas, 2000);
            }

            $(document).ready(function() {
                loadDatas();
            });
        </script>
    </body>
</html>