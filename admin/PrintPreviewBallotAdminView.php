<head>
    <meta charset='utf-8'>
    <title>Demo for sort-table.js</title>
    <script type="text/javascript" src="<? echo plugin_dir_url( __FILE__ ) . 'js/sort-table.min.js'; ?>"></script>
    <style type="text/css">
        table { border: 1px solid black; border-collapse: collapse; }

        th, td { padding: 2px 5px; border: 1px solid black; }

        thead { background: #ddd; }

        table#demo2.js-sort-0 tbody tr td:nth-child(1),
        table#demo2.js-sort-1 tbody tr td:nth-child(2),
        table#demo2.js-sort-2 tbody tr td:nth-child(3),
        table#demo2.js-sort-3 tbody tr td:nth-child(4),
        table#demo2.js-sort-4 tbody tr td:nth-child(5),
        table#demo2.js-sort-5 tbody tr td:nth-child(6),
        table#demo2.js-sort-6 tbody tr td:nth-child(7),
        table#demo2.js-sort-7 tbody tr td:nth-child(8),
        table#demo2.js-sort-8 tbody tr td:nth-child(9),
        table#demo2.js-sort-9 tbody tr td:nth-child(10) {
            background: #dee;
        }

    </style>
</head>
<!--
Sort Types:
js-sort-asc: ascending sort
js-sort-desc: descending sort
js-sort-0: zero-based number of the sorted column
js-sort-string: sort by string
js-sort-date: sort by date
js-sort-number: sort by number
js-sort-last: sort by the last word
js-sort-input: sort by the input value


-->
<div class="wp-print-ballot-container">
    <div class="wp-print-ballot-inner-table">
        <table class="js-sort-table" id="demo1">
            <thead>
            <tr>
                <th class="js-sort-string">First Name</th>
                <th class="js-sort-string">Last Name</th>
                <th class="js-sort-number">Employee ID</th>
                <th class="js-sort-string">Ballot Serial No.</th>
                <th class="js-sort-string">Ballot Status</th>
                <th class="js-sort-string">Address 1</th>
                <th class="js-sort-string">Address 2</th>
                <th class="js-sort-string">City</th>
                <th class="js-sort-string">State</th>
                <th class="js-sort-number">Zip</th>
                <th class="js-sort-number">Date Received</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-14-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-15-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-16-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-17-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-18-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-19-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-20-2020</td>
            </tr>
            <tr>
                <td>Bijan</td>
                <td>Markes</td>
                <td>10155</td>
                <td>CP1015512</td>
                <td>Pending Receipt</td>
                <td>1 Hacker Way</td>
                <td>#0x0A</td>
                <td>San Jose</td>
                <td>CA</td>
                <td>95119</td>
                <td>11-22-2020</td>
            </tr>

            </tbody>
        </table>
    </div>
</div>
