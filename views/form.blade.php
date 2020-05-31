<form action="../search.php" method="get" style="display: -webkit-flex; display: flex; -webkit-justify-content: space-around; justify-content: space-around; -webkit-align-items: center; align-items: center; width: 880px; margin: 20px auto; padding: 10px; background-color: #fff; border: 2px solid #333333;">
    <div style="height: 60px; width: 160px;">
        <label for="post_datetime">日付</label><input type="date" name="post_datetime" id="post_datetime" value="{{ $_GET['post_datetime'] ?: $cookie['post_datetime'] }}">
    </div>
    <div style="height: 60px; width: 160px;">
        <label for="url">URL</label><input type="text" name="url" id="url" value="{{ $_GET['url'] ?: $cookie['url']}}" style="margin-top: 6px; height: 20px;">
    </div>
    <div style="height: 60px; width: 160px;">
        <label for="user_name">ユーザー名</label><input type="text" name="user_name" id="user_name" value="{{ $_GET['user_name'] ?: $cookie['user_name'] }}" style="height: 20px;">
    </div>
    <div style="height: 60px; width: 160px;">
        <label for="server_number">サーバー番号</label><input type="text" name="server_number" id="server_number" value="{{ $_GET['server_number'] ?: $cookie['server_number'] }}" style="height: 20px;">
    </div>
    <div style="height: 60px; width: 160px;">
        <label for="entry_number">エントリーNo. (>=)</label><input type="text" name="entry_number" id="entry_number" value="{{ $_GET['entry_number'] ?: $cookie['entry_number'] }}" style="height: 20px;">
    </div>
    <input type="submit" value="検索">
</form>
@if ($data['error'] !== NULL)
    <div style="width: 900px; margin: 20px auto;">
    @foreach($data['error'] as $error)
        <p style="color: red;">{{ $error }}</p>
    @endforeach
    </div>
@endif

