<div class = "mt-5 mb-3">
    <video id="video1" width="1280" height="720" controls >
        <source src=null type="video/mp4">
        Your browser does not support mp4 videos.
    </video>
    
    <h3 class='display-6 text-center'>Your videos</h3>

    <button class="btn btn-light" type="button" onclick="setSrc({{ Js::from($current_video) }})">Set video</button>
    @foreach ($videos as $video)
        <div class="container-md mt-3">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-primary">
                    <input type="radio" name="options" id="option1" autocomplete="off" wire:click="$set('current_video', '{{asset($video->path)}}')"/>
                    Select {{$video->title}}
                </label>
            </div>
        </div>
    @endforeach


</div>

<script> 
    var myVideo = document.getElementById("video1"); 
    function setSrc(newSrc) {
        myVideo.src=newSrc
    }
</script>
