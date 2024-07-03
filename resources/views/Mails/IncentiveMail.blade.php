<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TARGET AND INCENTIVES</title>
</head>
<body>  
    <h2>TARGET AND INCENTIVES</h2>
    <p>Dear {{ $details['bo_name'] }}</p>

    <h3>TARGETS</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>H.Q.</th>
                <th>APRIL-MAY-JUNE-TARGET</th>
                <th>JULY-AUGUST-SEPTEMBER-TARGET</th>
                <th>OCTOBER-NOVEMBER-DECEMBER-TARGET</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $details['bo_name'] }}</td>
                <td>{{ $details['headquarter'] }}</td>
                <td>{{ $details['april_may_june_target'] }}</td>
                <td>{{ $details['july_aug_sept_target'] }}</td>
                <td>{{ $details['oct_nov_dec_target'] }}</td>
                <td>{{ $details['april_may_june_target'] + $details['july_aug_sept_target'] + $details['oct_nov_dec_target'] }}</td>
            </tr>
        </tbody>
    </table>

    @if($details['april_may_june_incentive'] > 0 || $details['july_aug_sept_incentive'] > 0 || $details['oct_nov_dec_incentive'] > 0)
        <div>
            <h3>INCENTIVES</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>H.Q.</th>
                        <th>APRIL-MAY-JUNE-INCENTIVE</th>
                        <th>JULY-AUGUST-SEPTEMBER-INCENTIVE</th>
                        <th>OCTOBER-NOVEMBER-DECEMBER-INCENTIVE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $details['bo_name'] }}</td>
                        <td>{{ $details['headquarter'] }}</td>
                        <td>{{ $details['april_may_june_incentive'] }}</td>
                        <td>{{ $details['july_aug_sept_incentive'] }}</td>
                        <td>{{ $details['oct_nov_dec_incentive'] }}</td>
                        <td>{{ $details['april_may_june_incentive'] + $details['july_aug_sept_incentive'] + $details['oct_nov_dec_incentive'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
   
    <p>Let's embrace this opportunity with enthusiasm and determination as we work together towards even greater achievements in the months ahead.</p>

    <h3>Best Regards!</h3>
    <p>Regards,<br>Priyanka Gupta<br>Product Executive<br>Acmedix Pharma</p>

</body>
</html>
