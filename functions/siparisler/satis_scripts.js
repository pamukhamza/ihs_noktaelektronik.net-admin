// B2B Orders page functionality
$(document).ready(function() {
    // Initialize variables
    const selectedIds = [];
    const baseUrl = window.location.origin + '/admin/';
    
    // Initialize DataTable
    function initializeDataTable() {
        const sDurum = $('.sDurum').val();
        const satis_id = $('.satis_id').val();
        return $('#orders-table').DataTable({
            rowId: 'id',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json',
                processing: "Yükleniyor...",
                search: "Ara:",
                lengthMenu: "Göster _MENU_ kayıt",
                info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                infoEmpty: "Kayıt yok",
                infoFiltered: "(_MAX_ kayıt içerisinden bulunan)",
                emptyTable: "Tabloda veri bulunmuyor",
                zeroRecords: "Eşleşen kayıt bulunamadı"
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: `${baseUrl}functions/siparisler/server_satissiparis.php`,
                type: 'POST',
                data: function(d) { 
                    d.sDurum = sDurum;
                    d.satis_id = satis_id;
                    // Debug: Log the data being sent
                    console.log('DataTables request data:', d);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                    console.error('Server response:', xhr.responseText);
                    
                    let errorMessage = 'Veriler yüklenirken bir hata oluştu.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: errorMessage
                    });
                },
                dataSrc: function(json) {
                    // Debug: Log the received data
                    console.log('Server response:', json);
                    
                    if (json.error) {
                        console.error('Server returned error:', json.error);
                        throw new Error(json.error);
                    }
                    return json.data;
                }
            },
            columns: getDataTableColumns(),
            order: [[6, 'desc']],
            drawCallback: handleDrawCallback,
            colReorder: true,
            pageLength: 25,
            stateSave: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            responsive: true
        });
    }

    // Get DataTable columns configuration
    function getDataTableColumns() {
        const showActionsColumn = true; // This can be controlled by permissions
        const baseColumns = [
            showActionsColumn ? {
                data: "id",
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="row-checkbox onay-checkbox" data-id="${data}">`;
                }
            } : null,
            {
                class: 'details-control',
                orderable: false,
                data: null,
                render: function() {
                    return '<i class="toggle-icon fa-solid fa-caret-right fa-xl"></i>';
                }
            },
            { data: "siparis_no" },
            {
                data: null,
                render: function(data) {
                    return `${data.uye_ad} ${data.uye_soyad}`;
                }
            },
            { data: "uye_firmaadi" },
            { data: "uye_email" },
            { data: "tarih" },
            {
                data: "durum",
                render: function(data) {
                    const statusMap = {
                        '1': "Yeni Sipariş",
                        '2': "Sipariş Onaylandı",
                        '3': "Kargolama Aşamasında",
                        '4': "Kargolandı",
                        '5': "Teslim Edildi",
                        '6': "İptal Edildi",
                        '7': "Müşteri İptal Etti",
                        '8': "Arşivlendi"
                    };
                    return statusMap[data] || "";
                }
            },
            {
                data: "odeme_sekli",
                render: function(data) {
                    return `<span class="fw-bold text-danger">${data}</span>`;
                }
            },
            {
                data: "kargo_firmasi",
                render: function(data) {
                    const cargoMap = {
                        '0': "Mağazadan Teslim Alınacak",
                        '1': "Özel Kargo",
                        '2': "Yurtiçi Kargo"
                    };
                    return cargoMap[data] || "";
                }
            },
            {
                data: "toplam",
                render: function(data) {
                    const formattedData = parseFloat(data.replace(',', '.')).toLocaleString('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    return `<span class="fw-bold fs-13">${formattedData}₺</span>`;
                }
            }
        ];
        
        return baseColumns.filter(Boolean);
    }

    // Handle DataTable draw callback
    function handleDrawCallback(settings) {
        const api = this.api();
        
        $('.details-control', api.table().container()).off('click').on('click', function() {
            const tr = $(this).closest('tr');
            const row = api.row(tr);
            const icon = $(this).find('.toggle-icon');
            
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-caret-down').addClass('fa-caret-right');
            } else {
                row.child(formatChildRow(row.data())).show();
                tr.addClass('shown');
                icon.removeClass('fa-caret-right').addClass('fa-caret-down');
            }
        });
    }

    // Format child row data
    function formatChildRow(data) {
        const childTableId = `childTable_${data.id}`;
        const tableHTML = `
            <table id="${childTableId}" class="child-table">
                <thead>
                    <tr>
                        <th>Fotoğraf</th>
                        <th>Stok Kodu</th>
                        <th>Urun Adı</th>
                        <th>Miktar</th>
                        <th>Birim Fiyat Döviz</th>
                        <th>Birim Fiyat TL</th>
                        <th>Toplam Tutar TL</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        `;

        const childRow = `<div>${tableHTML}</div>`;

        // Load child table data
        loadChildTableData(childTableId, data.id);
        
        return childRow;
    }

    // Load child table data via AJAX
    function loadChildTableData(tableId, orderId) {
        
        const requestData = new FormData();
        requestData.append('id', orderId);
 
        
        $.ajax({
            url: `${baseUrl}functions/siparisler/server_siparis_alt.php`,
            type: 'POST',
            data: requestData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {
                console.log('Sending AJAX request...');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function(response) {
                console.log('Child table response:', response);
                if (response && response.success) {
                    if (Array.isArray(response.data)) {
                        console.log('Initializing child table with data array of length:', response.data.length);
                        initializeChildTable(tableId, response.data);
                    } else {
                        console.error('Response data is not an array:', response.data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Geçersiz veri formatı alındı'
                        });
                    }
                } else {
                    console.error('Server returned error:', response ? response.error : 'No response');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: response && response.message ? response.message : 'Sipariş detayları yüklenirken bir hata oluştu.'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error Details:', {
                    status: jqXHR.status,
                    statusText: jqXHR.statusText,
                    responseText: jqXHR.responseText,
                    textStatus: textStatus,
                    errorThrown: errorThrown
                });
                
                let errorMessage = 'Sipariş detayları yüklenirken bir hata oluştu.';
                try {
                    if (jqXHR.responseText) {
                        console.log('Raw response:', jqXHR.responseText);
                        const response = JSON.parse(jqXHR.responseText);
                        console.log('Parsed error response:', response);
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.error) {
                            errorMessage = response.error;
                        }
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: errorMessage
                });
            }
        });
    }

    // Initialize child table
    function initializeChildTable(tableId, data) {
        console.log('Initializing child table with data:', data);
        try {
            $(`#${tableId}`).DataTable({
                data: data,
                columns: getChildTableColumns(),
                searching: false,
                lengthChange: false,
                info: false,
                paging: false,
                ordering: false,
                language: {
                    emptyTable: "Ürün bulunamadı"
                }
            });
        } catch (error) {
            console.error('Child table initialization error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: 'Sipariş detayları görüntülenirken bir hata oluştu.'
            });
        }
    }

    // Get child table columns configuration
    function getChildTableColumns() {
        return [
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `<div class="m-r-10">
                        <img width="45" class="rounded" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/${data.foto}"/>
                    </div>`;
                }
            },
            {
                data: 'UrunKodu',
                render: function(data, type, row) {
                    return `<a target="_blank" href="${baseUrl}tr/urun/${row.seo_link}">${data}</a>`;
                }
            },
            {
                data: 'UrunAdiTR',
                render: function(data, type, row) {
                    return `<a target="_blank" href="${baseUrl}tr/urun/${row.seo_link}">${data}</a>`;
                }
            },
            { data: 'adet' },
            { data: 'birim_fiyat' },
            {
                data: null,
                render: function(data) {
                    const price = data.DSF4 ? 
                        (data.birim_fiyat * data.dolar_satis) : 
                        data.birim_fiyat;
                    
                    return formatPrice(price);
                }
            },
            {
                data: null,
                render: function(data) {
                    const total = data.DSF4 ? 
                        (data.birim_fiyat * data.dolar_satis * data.adet) : 
                        (data.birim_fiyat * data.adet);
                    
                    return formatPrice(total);
                }
            }
        ];
    }

    // Format price with Turkish Lira symbol
    function formatPrice(price) {
        return price.toLocaleString('de-DE', {
            minimumFractionDigits: 4,
            maximumFractionDigits: 4
        }) + '₺';
    }

    // Handle checkbox changes
    $('#orders-table').on('change', '.row-checkbox', function() {
        selectedIds.length = 0; // Clear array
        $('#orders-table .row-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
    });

    // Handle order status changes
    function handleOrderStatusChange(action, targetStatus) {
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Lütfen en az bir sipariş seçin.'
            });
            return;
        }

        const requests = selectedIds.map(id => {
            return $.ajax({
                url: `${baseUrl}${action}`,
                method: 'POST',
                data: { type: targetStatus, sip_id: id }
            });
        });

        Promise.all(requests)
            .then(() => {
                window.location.href = `${baseUrl}pages/b2b/b2b-siparisler.php?sDurum=${targetStatus}&w=noktab2b`;
            })
            .catch(error => {
                console.error('Status change failed:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'İşlem sırasında bir hata oluştu.'
                });
            });
    }

    // Button click handlers
    $('.onayla-btn').click(() => handleOrderStatusChange('fonksiyonlar.php', 'siparis_onay'));
    $('.kargo-numara-ver-btn').click(() => handleOrderStatusChange('kargo_gonder.php', '3'));
    $('.teslim-edildi-btn').click(() => handleOrderStatusChange('fonksiyonlar.php', 'teslim_edildi'));

    // Print cargo labels
    $('.yazdir-btn').click(function() {
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Lütfen en az bir sipariş seçin.'
            });
            return;
        }

        selectedIds.forEach(function(sip_id) {
            $.ajax({
                type: 'POST',
                url: `${baseUrl}fonksiyonlar.php`,
                data: { id: sip_id, tur: 'kargo_barkod' }
            })
            .done(function(response) {
                const cleanedResponse = response.replace(/"/g, '');
                window.open(`${baseUrl}assets/uploads/kargo/${cleanedResponse}`, '_blank');
            })
            .fail(function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'Barkod yazdırma işlemi başarısız oldu.'
                });
            });
        });
    });

    // Edit order handler
    $(document).on('click', '.edit-order', function() {
        const orderId = $(this).data('order-id');
        window.location.href = `${baseUrl}pages/b2b/b2b-siparisdetay.php?id=${orderId}&w=noktab2b`;
    });

    // Initialize tooltips with error handling
    try {
        $('[data-bs-toggle="tooltip"]').tooltip();
    } catch (error) {
        console.error('Tooltip initialization error:', error);
    }

    // Initialize the main DataTable with error handling
    try {
        const ordersTable = initializeDataTable();
    } catch (error) {
        console.error('DataTable initialization error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Tablo yüklenirken bir hata oluştu.'
        });
    }
}); 