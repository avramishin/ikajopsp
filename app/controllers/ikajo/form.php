<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Form</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
   </head>
<body>
<h3>Test Payment</h3>
<form action="<?=url("ikajo/sale/create")?>">
    <table>
        <tr>
            <td>Amount</td>
            <td><input type="text" name="order_amount" value="10.00"/></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><input type="text" name="description" value="whatever payment"/></td>
        </tr>
        <tr>
            <td>Payer firstname</td>
            <td><input type="text" name="payer_first_name" value="Donald"/></td>
        </tr>
        <tr>
            <td>Payer lastname</td>
            <td><input type="text" name="payer_last_name" value="Trump"/></td>
        </tr>
        <tr>
            <td>Payer address</td>
            <td><input type="text" name="payer_address" value="1 Classified Str"/></td>
        </tr>

        <tr>
            <td>Payer country</td>
            <td><input type="text" name="payer_country" value="US"/></td>
        </tr>
        <tr>
            <td>Payer state</td>
            <td><input type="text" name="payer_state" value="TX"/></td>
        </tr>
        <tr>
            <td>Payer city</td>
            <td><input type="text" name="payer_city" value="The City"/>
            </td>
        </tr>
        <tr>
            <td>Payer zip</td>
            <td><input type="text" name="payer_zip" value="12345"/></td>
        </tr>
        <tr>
            <td>Payer email</td>
            <td><input type="text" name="payer_email" value="donald@trump.com"/></td>
        </tr>
        <tr>
            <td>Payer phone</td>
            <td><input type="text" name="payer_phone" value="+19992872282"/></td>
        </tr>
        <tr>
            <td>Card number</td>
            <td><input type="text" name="card_number" value="4111111111111111"/></td>
        </tr>
        <tr>
            <td>Card exp. month</td>
            <td><input type="text" name="card_exp_month" value="01"/></td>
        </tr>
        <tr>
            <td>Card exp. year</td>
            <td><input type="text" name="card_exp_year" value="2020"/></td>
        </tr>
        <tr>
            <td>Card CVV</td>
            <td><input type="text" name="card_cvv2" value="123"/></td>
        </tr>
    </table>

    <input type="hidden" name="success_url" value="<?=url('ikajo/success')?>" />
    <input type="hidden" name="error_url" value="<?=url('ikajo/error')?>" />
    <input type="submit" value="Make Payment" />
</form>
</body>
</html>