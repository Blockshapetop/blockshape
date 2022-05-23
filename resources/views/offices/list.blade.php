@inject('offices', 'App\Repositories\DepartmentsRepository')

<p class="well">
    Dacă ai întrebări poți să te apropii în oricare filială Unica Sport pentru a primi explicația Testării
    Nutriționale și Fiziologice
</p>

<div class="tabel clear office-list">
    <div class="cont_tbl clear">
        @foreach($offices->all() as $office)
            <div class="celula">
                <ul>
                    <li><h3>{{ $office->name }}</h3></li>
                    <li>{{ $office->address }}</li>
                    <li>{{ $office->phone }}</li>
                </ul>
            </div>
        @endforeach
    </div>
</div>
