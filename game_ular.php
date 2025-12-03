<?php
session_start();

//inisialisasi game jika belum ada
if(!isset($_SESSION['snake_game'])) {
    resetGame();
}

//tangani input kontrol
if(isset($_POST['direction'])) {
    handleInput($_POST['direction']);
}

//fungsi untuk mereset game
function resetGame() {
    $_SESSION['snake_game'] = [
        'snake' => [[10,10], [10,9], [10,8]], //posisi ular (kepala di depan)
        'direction' => 'right', //arah awal
        'food' => [5,5], //posisi makanan
        'score' => 0,
        'game_over' => false,
        'board_size' => 20 //ukuran papan permainan
    ];
}

//fungsi untuk menangani input arah
function handleInput($newDirection) {
    $game = &$_SESSION['snake_game'];

    //validasi arah (tidak bisa berbalik arah 180 derajat)
    $opposites = [
        'up' =>'down',
        'down' => 'up',
        'left' => 'right',
        'right' => 'left',
    ];

    if ($game['game_over']) {
        resetGame();
        return;
    }

    if ($newDirection !== $opposites[$game['direction']]) {
        $game['direction'] = $newDirection;
    }

    //pindahkan ular
    moveSnake();
}

//fungsi untuk memindahkan ular
function moveSnake(){
    $game = &$_SESSION['snake_game'];
    $snake = &$game['snake'];
    $direction = $game['direction'];
    $board_size = $game['board_size'];

    //tentukan posisi kepala baru bedasarkan arah
    $head= $snake[0];
    $newHead = $head;

    switch ($direction) {
        case 'up':
            $newHead = [$head[0], ($head[1] - 1 + $board_size) % $board_size];
            break;
        case 'down':
            $newHead = [$head[0], ($head[1] + 1) % $board_size];
            break;
        case 'left':
            $newHead = [($head[0] - 1 + $board_size) % $board_size, $head[1]];
            break;
        case'right':
            $newHead = [($head[0] + 1) % $board_size, $head[1]];
            break;
    }

    //cek tabrakan dengan diri sendiri
    foreach ($snake as $segment) {
        if($segment[0] == $newHead [0] && $segment[1] == $newHead[1]) {
            $game['game_over'] = true;
            return;
        }
    }

    //tambahkan kepala baru
    array_unshift($snake, $newHead);
    
    //cek apkah ular memakan makanan
    if($newHead[0] == $game['food'][0] && $newHead[1] == $game['food'][1]) {
        $game['score']++;
        generateFood();
    } else {
        //hapus ekor jika tidak makan
        array_pop($snake);
    }
}

//fungsiuntuk menghasilkan makanan baru
function generateFood(){
    $game =&$_SESSION['snake_game'];
    $snake = $game['snake'];
    $board_size = $game['board_size'];

    do {
        $food = [rand(0, $board_size - 1), rand(0, $board_size - 1)];
        $onSnake = false;

        foreach ($snake as $segment) {
            if ($segment[0] == $food[0] && $segment[1] == $food[1]) {
                $onSnake = true;
                break;
            }
    }
} while ($onSnake);

$game['game'] = $food;

}
?>
<!DOCTYPE html>
<html lang="id"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Ular (snake) - PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0f0f0;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        .game-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .game-board {
            display: grid;
            grid-template-columns: repeat(<?php echo $_SESSION['snake_game']['board_size']; ?>, 20px);
            grid-template-rows: repeat(<?php echo $_SESSION['snake_game']['board_size']; ?>, 20px);
            gap: 1px;
            background-color: #333;
            border: 2px solid #333;
            margin-bottom: 20px;
        }

        .cell {
            width: 20px;
            height: 20px;
            background-color: #cef07fff;
        }

        .snake {
            background-color: #30b4e0ff;
        }
        .snake-head {
            background-color: #e98d8aff;
        }
        .food {
            background-color: #279150ff;
            border-radius: 50%;
        }
        .controls {
            display: grid;
            grid-template-areas:
                ". up ."
                "left . right"
                ". down .";
            gap: 10px;
            margin-bottom: 20px;
        }

        button {
            padding: 15px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            background-color: #2196f3;
            color: white;
            cursor: pointer;
            transition:background-color 0.3s;
        }

        button:hover {
            background-color: #0b7dda;
        }
        .up { grid-area: up; }
        .down { grid-area: down; }
        .left { grid-area: left; }
        .right { grid-area: right; }

        .game-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .game-over {
            color: #f44336;
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .restart {
            background-color: #ff9800;
        }

        .restart:hover {
            background-color: #e68900;
        }
    </style>
</head>
<body>
    <h1>Game Ular (Snake) - PHP</h1>

    <div class="game-info">
        <p>skor: <?php echo $_SESSION['snake_game']['score']; ?></p>
        <?php if ($_SESSION['snake_game']['game_over']): ?>
            <p class="game-over">Game Over!</p>
        <?php endif; ?>
    </div>

    <div class="game-container">
        <div class="game-board">
            <?php
            $game = $_SESSION['snake_game'];
            $snake = $game['snake'];
            $food = $game['food'];
            $board_size = $game['board_size'];

            for ($y = 0; $y < $board_size; $y++) {
                for ($x = 0; $x < $board_size; $x++) {
                    $class = 'cell';

                    // cek apkah sel adalah bagian dari ular
                    $isSnake = false;
                    $isHead = false;
                    foreach ($snake as $index => $segment) {
                        if ($segment[0] == $x && $segment[1] == $y) {
                            $isSnake = true;
                            if ($index === 0) {
                                $isHead = true;
                            }
                            break;
                        }
                    }
                    if ($isHead) {
                        $class .= ' snake-head';
                    } elseif ($isSnake) {
                        $class .= ' snake';
                    } elseif ($food[0] == $x && $food[1] == $y) {
                        $class .= 'food';
                    }
                    
                    echo "<div class='$class'></div>";
                }
            }
            ?>
        </div>

        <form method="post" class="controls">
            <button type="submit" name="direction" value="up" class="up">↑</button>
            <button type="submit" name="direction" value="left" class="left">←</button>
            <button type="submit" name="direction" value="right" class="right">→</button>
            <button type="submit" name="direction" value="down" class="down">↓</button>
        </form>
        
        <?php if ($_SESSION['snake_game']['game_over']): ?>
            <form method="post">
                <button type="submit" name="direction" value="restart" class="restart">Main Lagi</button>
            </form>
        <?php endif; ?>
    </div>
    
    <div class="instructions">
        <h2>Cara Bermain:</h2>
        <p>Gunakan tombol panah untuk menggerakkan ular.</p>
        <p>Makan makanan (lingkaran merah) untuk menambah panjang ular dan skor.</p>
        <p>Hindari menabrak tubuh ular sendiri.</p>
    </div>
</body>
</html>
    </div>
</body>
</html>