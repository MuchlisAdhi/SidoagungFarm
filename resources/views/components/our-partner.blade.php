@foreach ($testimoni as $t)
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="feature-info text-center">
            <div class="feature-info-icon">
                <img class="img-fluid center-block mx-auto list-service-image w-50"
                    src="{{ route('main.getResource', ['id' => $t->photo]) }}" alt="">
            </div>
            <div class="feature-info-content">
                <h4 class="mb-3 feature-info-title">{{Str::headline($t->name)}}</h4>
                <p>{{$t->testimoni}}</p>
            </div>
        </div>
    </div>
@endforeach