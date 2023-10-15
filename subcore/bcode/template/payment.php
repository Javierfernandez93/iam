<style type="text/css">
table
{
    border-collapse: collapse;
    color: #555;
    font-family: Arial;
    margin-bottom: 12%;
}

th
{
    padding: 10px 22px;
}

td
{
    font-size: 16px;
    padding: 7px 22px;
}
.center
{
    text-align: center;
}
.red
{
    color: red;
}
.muted
{
    color: #AAA;
}
.b-gray
{
    background-color: #999;
}
.t-white
{
    color: #fff;
}
.f-big
{
    font-size: 18px;

}
.f-line
{
    line-height: 25px;
    padding: 6px 0 8px 0;
}
.f-right
{
    margin-top: -82px;
    float: right;
    margin-left: -28px;
}
.img-arrow-two
{
    margin-top: -92px;
}
</style>
<page>
    
    
    <img stlye="width:100px;" src="../../subcore/bcode/template/logo_mtk.png">
    
    <table>
        <col style="width:50%;">
        <col style="width:50%;">
        <tr>
            <td class="center" style="border-bottom:1px solid #EEE;border-top:1px solid #BBB;border-left:1px solid #BBB;border-right:1px solid #BBB;">
                Nombre<br>
                <span class="muted"><?php echo $data['name'];?></span>
            </td>
            <td style="border-bottom:1px solid #EEE; border-top:1px solid #BBB;border-left:1px solid #BBB;border-right:1px solid #BBB;">
                Fecha de vencimiento<br>
                <span class="muted"><?php echo $data['expiration_date'];?></span>
            </td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid #EEE;border-left:1px solid #BBB;border-right:1px solid #BBB;">
                Correo electrónico<br>
                <span class="muted"><?php echo $data['email'];?></span>
            </td>
            <td class="center" style="border-left:1px solid #BBB;border-right:1px solid #BBB;">N.Orden <?php echo $data['shipping_number'];?></td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid #BBB;border-left:1px solid #BBB;border-right:1px solid #BBB;">
                Fecha de compra<br>
                <span class="muted"><?php echo $data['buy_date'];?></span>
            </td>
            <td class="center f-big f-line t-white b-gray" style="border-bottom:1px solid #BBB;border-left:1px solid #BBB;border-right:1px solid #BBB;">Total a pagar $<?php echo $data['ammount'];?> MXN</td>
        </tr>
    </table>
    <br>
    <table>
        <col style="width:33.33%;">
        <col style="width:33.33%;">
        <col style="width:33.33%;">
        <tr>
            <tr>
                <td class="center" style="border-top:1px solid #BBB;border-left:1px solid #BBB;">
                    <img src="../../subcore/bcode/template/s_one.png"><br>
                </td>
                <td class="center" style="border-top:1px solid #BBB;">
                    
                    <img src="../../subcore/bcode/template/s_two.png"><br>
                </td>
                <td class="center" style="border-top:1px solid #BBB;border-right:1px solid #BBB;">
                    
                    <img src="../../subcore/bcode/template/s_three.png"><br>
                </td>
            </tr>
            <tr>
                <td class="center" style="border-bottom:1px solid #CCC; border-top:1px solid #BBB;border-left:1px solid #BBB;">
                    Realice su <br>compra<br>                    
                     <img class="f-right" src="../../subcore/bcode/template/arrow.png"><br>
                     
                </td>
                <td class="center" style="border-bottom:1px solid #CCC; border-top:1px solid #BBB;">
                    Imprima este <br>documento<br>                
                     <img class="f-right img-arrow-two" src="../../subcore/bcode/template/arrow.png"><br> 
                </td>
                <td class="center" style="border-bottom:1px solid #CCC; border-top:1px solid #BBB;border-right:1px solid #BBB;">
                    Pague en <br>cualquier sucursal <br>OXXO
                </td>
            </tr>
        </tr>
    </table>
    <br>

    
    <table>
        <col style="width:70%;">
        <col style="width:30%;">
        <tr class="center">
            <td style="border-right:1px solid #555;border-top:1px solid #CCC;border-left:1px solid #BBB;">
                <span class="f-big red">Importante</span>
            </td>
            <td class="t-white f-line f-big b-gray">Pagar en tiendas</td>
        </tr>
        <tr>
            <td class="center" style="border-right:1px solid #CCC;border-bottom:1px solid #CCC;border-left:1px solid #BBB;">
                 <span class="muted">El presente pago solo es válido dentro de la fecha de vencimiento arriba mencionada<br> (3 días contando desde su emisión),<br>
                Una vez hecho tu pago no es necesario registrarlo <br>
                El lapso de validación de tu pago estará sujeto<br> a las primeras 24 Hrs. hábiles<br>
                Una vez validado tu pago MTk se pondrá  en contacto contigo.<br>
                </span>
                <br>
                <small>
                    <b>Cargos extra por servicio de compra en OXXO $10.00 MXN</b>
                </small>
            </td>
            <td style="border-bottom:1px solid #CCC;border-right:1px solid #BBB;">                
                <img stlye="width:100px;" src="../../subcore/bcode/template/oxxo_logo.png">
            </td>
        </tr>
    </table>
    <br>
    <table>
        <col style="width:100%;">
        <tr>
            <td class="center">    
                <img style="margin-top:40px;width:50%;" src="https://www.sumateahora.com/subcore/bcode/api.php?text=<?php echo $data['code_id']?>&bar_size=2">
            </td>
        </tr>
    </table>
</page>