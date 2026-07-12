<!DOCTYPE html>
<html>
<body>
    <form action="/api/analyze-risk" method="POST">
        @csrf
        <input type="text" name="news_text" placeholder="news_text" value="krisis ekonomi demo rugi"><br>
        <input type="number" name="weather_risk" placeholder="weather_risk" value="50"><br>
        <input type="number" name="inflation_risk" placeholder="inflation_risk" value="50"><br>
        <input type="number" name="currency_risk" placeholder="currency_risk" value="50"><br>
        <button type="submit">Kirim Data</button>
    </form>
</body>
</html>