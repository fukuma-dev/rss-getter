<div>
    <table border="1" style="table-layout: fixed; word-break: break-all; word-wrap: break-word; border-collapse: collapse; border: solid 3px grey; width: 900px; margin: 0 auto; font-size: 14px; color: #333; background-color: #fff;">
        <tr style="height: 50px; color: #fff; background-color: #333;">
            <th style="width: 80px; padding: 0 10px;">日付</th>
            <th style="width: 200px; padding: 0 10px;">URL</th>
            <th style="padding: 0 10px;">タイトル</th>
            <th style="padding: 0 10px;">概要</th>
        </tr>
        @if (!empty($results))
            @foreach ($results as $result)
                <tr class="table-content">
                    @foreach($result as $element)
                        <th style="overflow: hidden; width: 100%; height: 80px;">
                            <p style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; margin: 0;">{{ $element }}</p>
                        </th>
                    @endforeach
                </tr>
            @endforeach
        @endif
    </table>
</div>
