<form action="../../functions/banka/yapikredi_test/payment_request.php" method="POST">
    <label>Kart Numarası:</label>
    <input type="text" name="ccno" maxlength="16" required><br>

    <label>cardHolderName:</label>
    <input type="text" name="cardHolderName" ><br>

    <label>Son Kullanma Tarihi (YYAA):</label>
    <input type="text" name="expDate" maxlength="4" placeholder="2706" required><br>

    <label>CVV:</label>
    <input type="text" name="cvc" maxlength="3" required><br>

    <label>Tutar (TL):</label>
    <input type="text" name="amount" placeholder="12.34" required><br>

    <label>Taksit Sayısı:</label>
    <select name="installment">
        <option value="00">Peşin</option>
        <option value="02">2 Taksit</option>
        <option value="03">3 Taksit</option>
        <option value="06">6 Taksit</option>
    </select><br>

    <button type="submit">Ödeme Yap</button>
</form>
