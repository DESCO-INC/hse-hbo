<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px 10px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px;
           background:#ffffff;
           border-radius:5px;
           overflow:hidden;
           border-top: 8px solid #16a34a;">

                    <!-- Header -->
                    <tr>
                        <td style="color:#16a34a;padding:20px;">
                            <h2 style="margin:0;font-size:30px;">DESCO HSE SYSTEM</h2>
                            <h3 style="margin:0;font-size:18px;">Hazard & Behaviour Observation (HBO)</h3>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:20px;color:#333;font-size:13px;line-height:1.6;">

                            <p style="margin:0;">Hi <strong>{{ $recipientName }}</strong>,</p>

                            <p style="">A new HBO item has been added to the system. Please see the
                                summary details below:</p>

                            <p style="margin:0;">Reported by :<strong>{{ $reportedBy }}</strong> </p>
                            <p style="margin:0;">Business Unit :<strong>{{ $businessUnit }}</strong> </p>
                            <p style="margin:0;">Group :<strong>{{ $group }}</strong> </p>
                            <p style="margin:0;">Type :<strong>{{ $type }}</strong> </p>
                            <p style="margin:0;">Category :<strong>{{ $category }}</strong> </p>
                            <p style="margin:0;">Date Raised :<strong>{{ $dateRaised }}</strong> </p>
                            <p style="margin:0;">Date Due :<strong>{{ $dateDue }}</strong> </p>
                            <p style="margin:0;">Status :<strong>{{ $status }}</strong> </p>
                        </td>
                    </tr>

                    <!-- Options -->
                    <tr>
                        <td style="color:#16a34a;padding:20px;">
                            <a href="{{ $url }}"
                                style="display:inline-block; padding:10px 20px; background-color:#16a34a; color:#ffffff; text-decoration:none; border-radius:5px; font-size:14px;">
                                Click here to see full details
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:15px;text-align:center;font-size:12px;color:#666;">
                            © {{ date('Y') }} DESCO
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
