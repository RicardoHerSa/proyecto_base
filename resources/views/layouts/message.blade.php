
    @if(session('info'))
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="alert alert-info alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong> {{ session('info') }} </strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong> {{ session('success') }} </strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="alert alert-warning alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong> {{ session('warning') }} </strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('danger'))
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong> {{ session('danger') }} </strong>
                </div>
            </div>
        </div>
    </div>
    @endif