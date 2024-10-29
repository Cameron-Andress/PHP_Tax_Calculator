<!DOCTYPE html>
<html>
<head>
    <title>Tax Calculator</title>
</head>
<body>
    <?php
    function calculateTaxes($income) {
        $brackets = [
            [10275, 0.10],
            [41775, 0.12],
            [89075, 0.22],
            [170050, 0.24],
            [215950, 0.32],
            [539900, 0.35],
            [PHP_INT_MAX, 0.37]
        ];

        $taxOwed = 0;
        $remainingIncome = $income;
        $taxesByBracket = [];

        foreach ($brackets as $bracket) {
            $amountInBracket = min($remainingIncome, $bracket[0]);
            $taxForBracket = $amountInBracket * $bracket[1];
            $taxesByBracket[] = $taxForBracket;
            $taxOwed += $taxForBracket;
            $remainingIncome -= $amountInBracket;

            if ($remainingIncome <= 0) {
                break;
            }
        }

        return [$taxOwed, $taxesByBracket];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST["name"]);
        $grossIncome = $_POST["grossIncome"];
        $totalDeductions = $_POST["totalDeductions"];

        if (is_numeric($grossIncome) && is_numeric($totalDeductions)) {
            $grossIncome = floatval($grossIncome);
            $totalDeductions = floatval($totalDeductions);

            if ($totalDeductions < 12950) {
                $totalDeductions = 12950;
            }

            $adjustedGrossIncome = $grossIncome - $totalDeductions;

            list($totalTaxesOwed, $taxesByBracket) = calculateTaxes($adjustedGrossIncome);

            echo "<h2>Tax Calculator Results for $name</h2>";
            echo "<p>Gross Income: $" . number_format($grossIncome, 2) . "</p>";
            echo "<p>Total Deductions: $" . number_format($totalDeductions, 2) . "</p>";
            echo "<p>Adjusted Gross Income: $" . number_format($adjustedGrossIncome, 2) . "</p>";

            $bracketLabels = ["10%", "12%", "22%", "24%", "32%", "35%", "37%"];
            foreach ($taxesByBracket as $index => $tax) {
                echo "<p>Taxes Owed at " . $bracketLabels[$index] . " bracket: $" . number_format($tax, 2) . "</p>";
            }

            echo "<p>Total Taxes Owed: $" . number_format($totalTaxesOwed, 2) . "</p>";
            echo "<p>Taxes Owed as percentage of gross income: " . number_format(($totalTaxesOwed / $grossIncome) * 100, 2) . "%</p>";
            echo "<p>Taxes Owed as percentage of adjusted gross income: " . number_format(($totalTaxesOwed / $adjustedGrossIncome) * 100, 2) . "%</p>";
        } else {
            echo "<p>Please enter numeric values for gross income and total deductions.</p>";
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Name: <input type="text" name="name"><br>
        Gross Income: <input type="text" name="grossIncome"><br>
        Total Deductions: <input type="text" name="totalDeductions"><br>
        <input type="submit" value="Calculate">
    </form>
</body>
</html>
