document.addEventListener('DOMContentLoaded', () => {
    const tripTypeRadios = document.querySelectorAll('input[name="tripType"]');
    const returnDateGroup = document.getElementById('returnDateGroup');
    const retDateInput = document.getElementById('retDate');
    const depDateInput = document.getElementById('depDate');
    
    const departureSelect = document.getElementById('departure');
    const destinationSelect = document.getElementById('destination');

    // 日付選択の最小値を今日に制限
    const today = new Date().toISOString().split('T')[0];
    depDateInput.min = today;
    retDateInput.min = today;

    // 1. JSONファイルから就航地データを取得してプルダウンに反映
    fetch('airports.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('JSONの読み込みに失敗しました');
            }
            return response.json();
        })
        .then(airports => {
            // 初期状態の「読込中...」をクリア
            departureSelect.innerHTML = '<option value="">出発地を選択</option>';
            destinationSelect.innerHTML = '<option value="">目的地を選択</option>';

            // 各空港データをoptionタグとして追加
            airports.forEach(airport => {
                const optDep = document.createElement('option');
                optDep.value = airport.code;
                optDep.textContent = airport.name;
                departureSelect.appendChild(optDep);

                const optDest = document.createElement('option');
                optDest.value = airport.code;
                optDest.textContent = airport.name;
                destinationSelect.appendChild(optDest);
            });
        })
        .catch(error => {
            console.error('エラー:', error);
            departureSelect.innerHTML = '<option value="">データ読み込みエラー</option>';
            destinationSelect.innerHTML = '<option value="">データ読み込みエラー</option>';
        });

    // 2. 旅程タイプ（往復・片道）の切り替え制御
    tripTypeRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'one-way') {
                returnDateGroup.classList.add('hidden');
                retDateInput.required = false;
                retDateInput.value = '';
            } else {
                returnDateGroup.classList.remove('hidden');
                retDateInput.required = true;
            }
        });
    });

    // 3. 重複チェックバリデーション（コード同士で比較）
    document.getElementById('bookingForm').addEventListener('submit', (e) => {
        const dep = departureSelect.value;
        const dest = destinationSelect.value;
        if (dep && dest && dep === dest) {
            alert('出発地と目的地には、異なる空港を選択してください。');
            e.preventDefault();
        }
    });
});