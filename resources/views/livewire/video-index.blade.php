<div class = "mt-5 mb-3">
    <video id="video1" width="1280" height="720" controls >
        <source src=null type="video/mp4">
        Your browser does not support mp4 videos.
    </video>

    @foreach ($videos as $video)
        <div class="container-md mt-3">  
            <div class="list-group">
                <button wire:click="$set('current_video', '{{asset($video->path)}}')">{{$video->title}}</button>
                <button onclick="setSrc({{ Js::from($current_video) }})">SetVid</button>
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
