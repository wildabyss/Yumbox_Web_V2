<?php

$lang['customer_invoice_subject'] = "Your payment at Yumbox is received";
$lang['customer_invoice_body'] = <<<EOT
<div style="width: 600px">
    <p>Dear {{ customer.name }},</p>
    <p>This email is the acknowledgement of your payment for the following order:</p>
    <table width="100%" cellspacing="10px">
        <tbody>
            {{# basket.foods_orders }}
            {{# . }}
            <tr style="border: 1px black solid">
                <td width="30%" valign="middle" style="text-align: right"><img src="{{path}}"></td>
                <td width="70%" valign="top">
                    <table width="100%" cellspacing="5px">
                        <tbody>
                            <tr>
                                <td width="30%" style="text-align: right">Name:</td>
                                <td width="70%"><b>{{ name }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Quantity:</td>
                                <td><b>{{ quantity }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Price:</td>
                                <td><b>\${{ price }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            {{/ . }}
            {{/ basket.foods_orders }}
        </tbody>
    </table>
    <br />
    <table cellspacing="5px">
        <tbody>
        <tr>
            <td colspan="2">Payment information</td>
        </tr>
        <tr>
        	<td style="text-align: right">Commission:</td>
        	<td><b>\${{ basket.commission }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right">Taxes:</td>
        	<td><b>\${{ basket.taxes }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right">Total cost:</td>
        	<td><b>\${{ basket.total_cost }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right; padding-top: 20px;">Your address:</td>
        	<td style="padding-top: 20px;"><b>{{ customer.address }}</b></td>
      	</tr>
        </tbody>
    </table>
</div>
EOT;

$lang['vendor_invoice_subject'] = "An order is ready for you";
$lang['vendor_invoice_body'] = <<<EOT
<div style="width: 600px">
    <p>Dear {{ vendor.name }},</p>
    <p>An order is ready for you:</p>
    <table width="100%" cellspacing="10px">
        <tbody>
            {{# order }}
            <tr style="border: 1px black solid">
                <td width="30%" valign="middle" style="text-align: right"><img src="{{path}}"></td>
                <td width="70%" valign="top">
                    <table width="100%" cellspacing="5px">
                        <tbody>
                            <tr>
                                <td width="30%" style="text-align: right">Name:</td>
                                <td width="70%"><b>{{ name }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Quantity:</td>
                                <td><b>{{ quantity }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Price:</td>
                                <td><b>\${{ price }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            {{/ order }}
        </tbody>
    </table>
    <br />
    <table cellspacing="5px">
        <tbody>
        <tr>
            <td colspan="2">Customer information</td>
        </tr>
        <tr>
        	<td style="text-align: right;">Name:</td>
        	<td><b>{{ customer.name }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right;">Email:</td>
        	<td><b>{{ customer.email }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right;">Address:</td>
        	<td><b>{{ customer.address }}</b></td>
      	</tr>
        </tbody>
    </table>
</div>
EOT;

$lang['customer_refund_subject'] = "Your order at Yumbox has been refunded";
$lang['customer_refund_body'] = <<<EOT
<div style="width: 600px">
    <p>Dear {{ customer.name }},</p>
    <p>This email is sent to you because the following order of your with Yumbox has been refunded:</p>
    <table width="100%" cellspacing="10px">
        <tbody>
            {{# order }}
            <tr style="border: 1px black solid">
                <td width="30%" valign="middle" style="text-align: right"><img src="{{path}}"></td>
                <td width="70%" valign="top">
                    <table width="100%" cellspacing="5px">
                        <tbody>
                            <tr>
                                <td width="30%" style="text-align: right">Name:</td>
                                <td width="70%"><b>{{ name }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Quantity:</td>
                                <td><b>{{ quantity }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Price:</td>
                                <td><b>\${{ price }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            {{/ order }}
        </tbody>
    </table>
    <br />
    <table cellspacing="5px">
        <tbody>
        <tr>
            <td colspan="2">More information</td>
        </tr>
        <tr>
        	<td style="text-align: right">Refunded amount:</td>
        	<td><b>\${{ amount }}</b></td>
      	</tr>
        	<td style="text-align: right; padding-top: 20px;">Explanation:</td>
        	<td style="padding-top: 20px;"><b>{{ explanation }}</b></td>
      	</tr>
        </tbody>
    </table>
</div>
EOT;

$lang['vendor_refund_subject'] = "An order has been canceled";
$lang['vendor_refund_body'] = <<<EOT
<div style="width: 600px">
    <p>Dear {{ vendor.name }},</p>
    <p>The following order has been canceled:</p>
    <table width="100%" cellspacing="10px">
        <tbody>
            {{# order }}
            <tr style="border: 1px black solid">
                <td width="30%" valign="middle" style="text-align: right"><img src="{{path}}"></td>
                <td width="70%" valign="top">
                    <table width="100%" cellspacing="5px">
                        <tbody>
                            <tr>
                                <td width="30%" style="text-align: right">Name:</td>
                                <td width="70%"><b>{{ name }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Quantity:</td>
                                <td><b>{{ quantity }}</b></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">Price:</td>
                                <td><b>\${{ price }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            {{/ order }}
        </tbody>
    </table>
    <br />
    <table cellspacing="5px">
        <tbody>
        <tr>
            <td colspan="2">Customer information</td>
        </tr>
        <tr>
        	<td style="text-align: right;">Name:</td>
        	<td><b>{{ customer.name }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right;">Email:</td>
        	<td><b>{{ customer.email }}</b></td>
      	</tr>
        <tr>
        	<td style="text-align: right;">Address:</td>
        	<td><b>{{ customer.address }}</b></td>
      	</tr>
      	</tr>
        	<td style="text-align: right; padding-top: 20px;">Explanation:</td>
        	<td style="padding-top: 20px;"><b>{{ explanation }}</b></td>
      	</tr>
        </tbody>
    </table>
</div>
EOT;
