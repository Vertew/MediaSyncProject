<div class = "mt-3 mb-3">

    <h3 class='display-6 text-center'>{{$video_title ?? "No video selected yet..."}}</h3>

    <video id="video1" width="1280" height="720" controls >
        <source src=null type="video/mp4">
        Your browser does not support mp4 videos.
    </video>
    
    <div class = "container-md mb-3 text-center">
        <h3 class='display-6 text-center'>Your videos</h3>
    </div>
    
    <button class="btn btn-light" type="button" onclick="setSrc({{ Js::from($current_video) }})">Set video</button>

    <div class = "container-md mb-3 text-center" style="max-height: 300px; overflow-y: auto;">
        @foreach ($videos as $video)
            <div class="container-md mt-3">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary">
                        <input type="radio" name="options" id="option1" autocomplete="off" wire:click="set_media({{$video}})"/>
                        Select {{$video->title}}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script> 
    var myVideo = document.getElementById("video1"); 
    function setSrc(newSrc) {
        myVideo.src=newSrc
    }
</script>
