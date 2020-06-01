@php
    if (empty($recordCounts) || empty($displayMax)) {
        return;
    }

    $max_page = (int)ceil($recordCounts/$displayMax);

    $params = $_GET;
    if (!isset($params['page_id'])) {
        $now = 1; // 設定されてない場合は1ページ目
    } else {
        $now = (int)$params['page_id'];
    }

    $queryString = [];
    foreach ($params as $key => $value) {
        if ($key == 'page_id') {
            continue;
        }
        array_push($queryString, "$key=$value");
    }
    if (!empty($queryString)) {
        $queryString = '&'.implode('&', $queryString);
    }
@endphp

<div style="display: -webkit-flex; display: flex; justify-content: center; margin: 50px auto; width: 300px; font-size: 30px;">
@for ($i = 1; $i <= $max_page; $i++)
    @php($url = "../search.php?page_id=$i$queryString")
    @if ($i === $now)
        <p style="font-weight: 900; margin: 0 10px;">{{ $i }}</p>
    @elseif ($i === 1 || $i === $max_page || ($i === $now + 1 && $now === $max_page -2) || ($i === $now - 1 && $now === 3))
        <p style="margin: 0 10px;">
            <a href="{{ $url }}">{{ $i }}</a>
        </p>
    @elseif ($i === $now + 1 && $now !== $max_page - 2)
        <p style="margin: 0 10px;">
            <a href="{{ $url }}">{{ $i }}</a><span style="margin-left: 20px;">...</span>
        </p>
    @elseif ($i === $now - 1 && $now !== 3)
        <p style="margin: 0 10px;">
            <span style="margin-right: 20px;">...</span><a href="{{ $url }}">{{ $i }}</a>
        </p>
    @endif
@endfor
</div>
