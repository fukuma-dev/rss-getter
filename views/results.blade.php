<div>
    <table border="1">
        <tr class="table-title" style="height: 50px">
            <th style="width: 80px;">日付</th>
            <th style="width: 200px;">URL</th>
            <th>タイトル</th>
            <th>概要</th>
        </tr>
        @if (!empty($results))
            @foreach ($results as $result)
                <tr class="table-content">
                    @foreach($result as $element)
                        <th>
                            <p style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; margin: 0;">{{ $element }}</p>
                        </th>
                    @endforeach
                </tr>
            @endforeach
        @endif
    </table>
</div>

<style>
    table {
        table-layout: fixed;
        word-break: break-all;
        word-wrap: break-word;
        border-collapse: collapse;
        border: solid 3px grey;
        width: 900px;
        margin: 0 auto;
        font-size: 14px;
        color: #333;
        background-color: #fff;
    }
    th {
        padding: 0 10px;
    }
    .table-title {
        height: 50px;
    }
    .table-title th {
        color: #fff;
        background-color: #333;
    }
    .table-content th {
        overflow: hidden;
        width: 100%;
        height: 80px
    }
</style>
