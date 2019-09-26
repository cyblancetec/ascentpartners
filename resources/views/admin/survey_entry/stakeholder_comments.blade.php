<table id="view-table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Email</th>
            <th>Stakeholder</th>
        </tr>
    </thead>
    <tbody>
       @foreach($stakeholder_comments as $stakeholder_comment)
            <tr>
                <td>{{ $stakeholder_comment->email }}</td>
                <td>{{ $stakeholder_comment->stakeholder_comment }}</td>
            </tr>                              
       @endforeach
    </tbody>
</table>