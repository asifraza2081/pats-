<?php
include "db.php";

$app_id = $_GET['id'];
$user_id = $_SESSION['user'];

// Fetch Data
$stmt = $conn->prepare("
SELECT a.*, j.job_title, j.fee,
       u.name, u.nic
FROM applications a
JOIN jobs j ON a.job_id = j.id
JOIN users u ON a.user_id = u.id
WHERE a.id = ? AND a.user_id = ?
");
$stmt->bind_param("ii", $app_id, $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Invalid Access");
}

// Challan No
$challan_no = "PATS-" . date("Y") . "-" . str_pad($app_id, 5, "0", STR_PAD_LEFT);


// Convert Number To Words
function numberToWords($num)
{
    $ones = array(
        0 => "",
        1 => "One",
        2 => "Two",
        3 => "Three",
        4 => "Four",
        5 => "Five",
        6 => "Six",
        7 => "Seven",
        8 => "Eight",
        9 => "Nine",
        10 => "Ten",
        11 => "Eleven",
        12 => "Twelve",
        13 => "Thirteen",
        14 => "Fourteen",
        15 => "Fifteen",
        16 => "Sixteen",
        17 => "Seventeen",
        18 => "Eighteen",
        19 => "Nineteen"
    );

    $tens = array(
        2 => "Twenty",
        3 => "Thirty",
        4 => "Forty",
        5 => "Fifty",
        6 => "Sixty",
        7 => "Seventy",
        8 => "Eighty",
        9 => "Ninety"
    );

    if ($num < 20) return $ones[$num];
    elseif ($num < 100)
        return $tens[floor($num / 10)] . " " . $ones[$num % 10];
    elseif ($num < 1000)
        return $ones[floor($num / 100)] . " Hundred " . numberToWords($num % 100);
    else
        return numberToWords(floor($num / 1000)) . " Thousand " . numberToWords($num % 1000);
}

$amount_words = numberToWords($data['fee']) . " Rupees Only";
?>

<!DOCTYPE html>
<html>

<head>
    <title>PATS Challan</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        .container {
            width: 1100px;
            margin: auto;
            margin-top: 20px;
        }

        .row {
            display: flex;
            gap: 20px;
        }

        .challan {
            width: 50%;
            background: #fff;
            border: 2px solid #000;
            padding: 15px;
            font-size: 13px;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .bank-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .note {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        .amount-box {
            border: 1px solid #000;
            padding: 5px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .print-btn {
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="print-btn">
            <button onclick="window.print()">Print Challan</button>
        </div>

        <div class="row">

            <!-- ================= LEFT COPY ================= -->
            <div class="challan">

                <div class="header">
                    PRIME ASSESSMENT & TESTING SERVICES-Pakistan<br>
                    ONLINE DEPOSIT SLIP<br>
                    (PATS COPY)
                </div>

                <br>

                <table class="table">
                    <tr>
                        <td>Challan No</td>
                        <td><?= $challan_no ?></td>
                    </tr>

                    <tr>
                        <td>Post</td>
                        <td><?= $data['job_title'] ?></td>
                    </tr>
                    <tr>
                        <td>Candidate</td>
                        <td><?= $data['name'] ?></td>
                    </tr>
                    <tr>
                        <td>CNIC</td>
                        <td><?= $data['nic'] ?></td>
                    </tr>
                </table>

                <br>

                <div class="bank-box">
                    <strong>Bank Details:</strong><br>
                    Allied Bank / UBL / MCB / HBL<br>
                    A/C Title: PATS-Pakistan<br>
                </div>

                <div class="amount-box">
                    <strong>Amount Rs:</strong> <?= number_format($data['fee']) ?><br>
                    <strong>In Words:</strong> <?= $amount_words ?><br>
                    Non Refundable / Non Transferable
                </div>

                <div class="note">
                    Note: Deposit Slip will not be accepted without Candidate CNIC.
                </div>

                <div class="footer">
                    <div>Applicant Signature</div>
                    <div>Cashier</div>
                    <div>Officer</div>
                </div>

            </div>


            <!-- ================= RIGHT COPY ================= -->
            <div class="challan">

                <div class="header">
                    PRIME ASSESSMENT & TESTING SERVICES-Pakistan<br>
                    ONLINE DEPOSIT SLIP<br>
                    (BANK COPY)
                </div>

                <br>

                <table class="table">
                    <tr>
                        <td>Challan No</td>
                        <td><?= $challan_no ?></td>
                    </tr>

                    <tr>
                        <td>Post</td>
                        <td><?= $data['job_title'] ?></td>
                    </tr>
                    <tr>
                        <td>Candidate</td>
                        <td><?= $data['name'] ?></td>
                    </tr>
                    <tr>
                        <td>CNIC</td>
                        <td><?= $data['nic'] ?></td>
                    </tr>
                </table>

                <br>

                <div class="bank-box">
                    <strong>Bank Details:</strong><br>
                    Allied Bank / UBL / MCB / HBL<br>
                    A/C Title: PATS-Pakistan<br>
                </div>

                <div class="amount-box">
                    <strong>Amount Rs:</strong> <?= number_format($data['fee']) ?><br>
                    <strong>In Words:</strong> <?= $amount_words ?><br>
                    Non Refundable / Non Transferable
                </div>

                <div class="note">
                    Note: Bank must return PATS copy to candidate.
                </div>

                <div class="footer">
                    <div>Applicant Signature</div>
                    <div>Cashier</div>
                    <div>Officer</div>
                </div>

            </div>

        </div>
    </div>

</body>

</html>