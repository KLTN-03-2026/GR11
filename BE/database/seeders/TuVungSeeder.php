<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuVungSeeder extends Seeder
{
    /**
     * Seed dữ liệu từ vựng phong phú: từ vựng tiếng Việt, hình ảnh thực tế, phân cấp độ khó.
     */
    public function run(): void
    {
        $u = 'https://images.unsplash.com';
        $q = 'auto=format&fit=crop&w=600&q=80';

        $catalog = [
            'Phát âm phụ âm L và N' => [
                ['tu_chuan' => 'Quả na', 'phien_am' => 'Quả na', 'cap_do' => 'de', 'img' => "$u/photo-1619566636858-adf3ef46400b?$q"],
                ['tu_chuan' => 'Cái lá', 'phien_am' => 'Cái lá', 'cap_do' => 'de', 'img' => "$u/photo-1528183429752-a97d0bf99b5a?$q"],
                ['tu_chuan' => 'Lúa vàng', 'phien_am' => 'Lúa vàng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1535090467336-9501f96eef89?$q"],
                ['tu_chuan' => 'Con lợn', 'phien_am' => 'Con lợn', 'cap_do' => 'de', 'img' => "$u/photo-1570141961143-693630623a48?$q"],
                ['tu_chuan' => 'Nụ cười', 'phien_am' => 'Nụ cười', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1494790108377-be9c29b29330?$q"],
                ['tu_chuan' => 'Loại lúa non', 'phien_am' => 'Loại lúa non', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1574943320219-553eb213f72d?$q"],
                ['tu_chuan' => 'Nông dân', 'phien_am' => 'Nông dân', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1574304439104-e87b1f44c7fe?$q"],
                ['tu_chuan' => 'Lồng đèn', 'phien_am' => 'Lồng đèn', 'cap_do' => 'kho', 'img' => "$u/photo-1534670007768-7cb86b42e444?$q"],
                ['tu_chuan' => 'Nước lã', 'phien_am' => 'Nước lã', 'cap_do' => 'kho', 'img' => "$u/photo-1556821552-7f41c5a31144?$q"],
                ['tu_chuan' => 'Lơn lớn', 'phien_am' => 'Lơn lớn', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1523788671040-e2917df1913f?$q"],
                ['tu_chuan' => 'Len lót', 'phien_am' => 'Len lót', 'cap_do' => 'de', 'img' => "$u/photo-1558618666-fcd25c85cd64?$q"],
                ['tu_chuan' => 'Nấm lây', 'phien_am' => 'Nấm lây', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Lá sen', 'phien_am' => 'Lá sen', 'cap_do' => 'de', 'img' => "$u/photo-1578431494494-da8e0ecc0355?$q"],
                ['tu_chuan' => 'Nêu lên', 'phien_am' => 'Nêu lên', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1516035069371-29a25244cad1?$q"],
                ['tu_chuan' => 'Lỗ lồng', 'phien_am' => 'Lỗ lồng', 'cap_do' => 'kho', 'img' => "$u/photo-1494976285917-425207a53b93?$q"],
            ],
            'Luyện dấu hỏi và dấu ngã' => [
                ['tu_chuan' => 'Quả bóng', 'phien_am' => 'Quả bóng', 'cap_do' => 'de', 'img' => "$u/photo-1589744323624-9984918e9d9e?$q"],
                ['tu_chuan' => 'Vẽ tranh', 'phien_am' => 'Vẽ tranh', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1513364776144-60967b0f800f?$q"],
                ['tu_chuan' => 'Cổ áo', 'phien_am' => 'Cổ áo', 'cap_do' => 'de', 'img' => "$u/photo-1591047139829-d91aecb6caea?$q"],
                ['tu_chuan' => 'Cỗ xe', 'phien_am' => 'Cỗ xe', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1532581133558-410373737145?$q"],
                ['tu_chuan' => 'Quả sung', 'phien_am' => 'Quả sung', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Vẽ hoa', 'phien_am' => 'Vẽ hoa', 'cap_do' => 'de', 'img' => "$u/photo-1578431494494-da8e0ecc0355?$q"],
                ['tu_chuan' => 'Cổ chân', 'phien_am' => 'Cổ chân', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1516035069371-29a25244cad1?$q"],
                ['tu_chuan' => 'Cơm chay', 'phien_am' => 'Cơm chay', 'cap_do' => 'de', 'img' => "$u/photo-1511689915389-ee4dc8151032?$q"],
                ['tu_chuan' => 'Cuốn sách', 'phien_am' => 'Cuốn sách', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1507842931343-583f20270319?$q"],
                ['tu_chuan' => 'Cộng hoà', 'phien_am' => 'Cộng hoà', 'cap_do' => 'kho', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Công chúa', 'phien_am' => 'Công chúa', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1519046904884-53103b34b206?$q"],
                ['tu_chuan' => 'Vổ vẻ', 'phien_am' => 'Vổ vẻ', 'cap_do' => 'kho', 'img' => "$u/photo-1533154772915-ae74d727e5f5?$q"],
                ['tu_chuan' => 'Vũng nước', 'phien_am' => 'Vũng nước', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1511379938547-c1f69b13d835?$q"],
                ['tu_chuan' => 'Cử động', 'phien_am' => 'Cử động', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1524995997946-a1c2e315a42f?$q"],
                ['tu_chuan' => 'Quả chuông', 'phien_am' => 'Quả chuông', 'cap_do' => 'de', 'img' => "$u/photo-1533634867381-75ce9dbafb39?$q"],
                ['tu_chuan' => 'Cơn gió', 'phien_am' => 'Cơn gió', 'cap_do' => 'de', 'img' => "$u/photo-1534531173927-aeb928cd54f7?$q"],
            ],
            'Từ vựng: Đồ dùng học tập' => [
                ['tu_chuan' => 'Bút chì', 'phien_am' => 'Bút chì', 'cap_do' => 'de', 'img' => "$u/photo-1510218830377-4e99b55c7422?$q"],
                ['tu_chuan' => 'Thước kẻ', 'phien_am' => 'Thước kẻ', 'cap_do' => 'de', 'img' => "$u/photo-1586075010620-227a6ca72353?$q"],
                ['tu_chuan' => 'Cặp sách', 'phien_am' => 'Cặp sách', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1553062407-98eeb64c6a62?$q"],
                ['tu_chuan' => 'Quyển vở', 'phien_am' => 'Quyển vở', 'cap_do' => 'de', 'img' => "$u/photo-1507842931343-583f20270319?$q"],
                ['tu_chuan' => 'Bút mực', 'phien_am' => 'Bút mực', 'cap_do' => 'de', 'img' => "$u/photo-1510218830377-4e99b55c7422?$q"],
                ['tu_chuan' => 'Tẩy chì', 'phien_am' => 'Tẩy chì', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1612198188060-c7c2a3b66eae?$q"],
                ['tu_chuan' => 'Bảng đen', 'phien_am' => 'Bảng đen', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1552664730-d307ca884978?$q"],
                ['tu_chuan' => 'Phấn trắng', 'phien_am' => 'Phấn trắng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1571235516501-6aa8b9e6c1b1?$q"],
                ['tu_chuan' => 'Bìa sơn', 'phien_am' => 'Bìa sơn', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1578301978162-7a0f5ad71eaf?$q"],
                ['tu_chuan' => 'Ghim bấm', 'phien_am' => 'Ghim bấm', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1519903904309-49a088fb63d1?$q"],
                ['tu_chuan' => 'Dây chun', 'phien_am' => 'Dây chun', 'cap_do' => 'de', 'img' => "$u/photo-1545912219-16d7d551f216?$q"],
                ['tu_chuan' => 'Khuôn vẽ', 'phien_am' => 'Khuôn vẽ', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1520763185298-1b434c919afe?$q"],
                ['tu_chuan' => 'Bộ màu', 'phien_am' => 'Bộ màu', 'cap_do' => 'de', 'img' => "$u/photo-1513364776144-60967b0f800f?$q"],
                ['tu_chuan' => 'Sách báo', 'phien_am' => 'Sách báo', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1524995997946-a1c2e315a42f?$q"],
                ['tu_chuan' => 'Bàn học', 'phien_am' => 'Bàn học', 'cap_do' => 'de', 'img' => "$u/photo-1592078615290-033ee584e267?$q"],
                ['tu_chuan' => 'Ghế ngồi', 'phien_am' => 'Ghế ngồi', 'cap_do' => 'de', 'img' => "$u/photo-1535746051778-4c8fb1642410?$q"],
                ['tu_chuan' => 'Giá sách', 'phien_am' => 'Giá sách', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1507842931343-583f20270319?$q"],
                ['tu_chuan' => 'Túi xách', 'phien_am' => 'Túi xách', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1553062407-98eeb64c6a62?$q"],
                ['tu_chuan' => 'Đèn bàn', 'phien_am' => 'Đèn bàn', 'cap_do' => 'de', 'img' => "$u/photo-1565641741215-d3e58c14d78f?$q"],
                ['tu_chuan' => 'Đồng hồ', 'phien_am' => 'Đồng hồ', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1523170335684-f1b0248e7fb8?$q"],
            ],
            'Từ vựng: Con vật nuôi' => [
                ['tu_chuan' => 'Con mèo', 'phien_am' => 'Con mèo', 'cap_do' => 'de', 'img' => "$u/photo-1514888286974-6c03e2ca1dba?$q"],
                ['tu_chuan' => 'Chú cún', 'phien_am' => 'Chú cún', 'cap_do' => 'de', 'img' => "$u/photo-1543466835-00a7907e9de1?$q"],
                ['tu_chuan' => 'Thỏ trắng', 'phien_am' => 'Thỏ trắng', 'cap_do' => 'de', 'img' => "$u/photo-1585110396000-c9ffd4e4b308?$q"],
                ['tu_chuan' => 'Chim vàng', 'phien_am' => 'Chim vàng', 'cap_do' => 'de', 'img' => "$u/photo-1444464666175-1642a9dbaaeb?$q"],
                ['tu_chuan' => 'Cá vàng', 'phien_am' => 'Cá vàng', 'cap_do' => 'de', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
                ['tu_chuan' => 'Rùa xanh', 'phien_am' => 'Rùa xanh', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1559827260-dc66d52bef19?$q"],
                ['tu_chuan' => 'Chuột nhắt', 'phien_am' => 'Chuột nhắt', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1586270052442-f08c4c1e1d00?$q"],
                ['tu_chuan' => 'Nhím nhỏ', 'phien_am' => 'Nhím nhỏ', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1588159619841-1b14b3a8cfe2?$q"],
                ['tu_chuan' => 'Thằn lằn', 'phien_am' => 'Thằn lằn', 'cap_do' => 'kho', 'img' => "$u/photo-1596854407944-bf87f6fdd49e?$q"],
                ['tu_chuan' => 'Rắn sọc', 'phien_am' => 'Rắn sọc', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1564760055775-85b4e88d0904?$q"],
            ],
            'Từ vựng: Gia đình thân yêu' => [
                ['tu_chuan' => 'Ông nội', 'phien_am' => 'Ông nội', 'cap_do' => 'de', 'img' => "$u/photo-1533227441973-500609341400?$q"],
                ['tu_chuan' => 'Bà ngoại', 'phien_am' => 'Bà ngoại', 'cap_do' => 'de', 'img' => "$u/photo-1518101645466-7795885ff8f8?$q"],
                ['tu_chuan' => 'Bố mẹ', 'phien_am' => 'Bố mẹ', 'cap_do' => 'de', 'img' => "$u/photo-1591526038358-b052af7f600c?$q"],
                ['tu_chuan' => 'Anh trai', 'phien_am' => 'Anh trai', 'cap_do' => 'de', 'img' => "$u/photo-1581952975470-a3093220556e?$q"],
                ['tu_chuan' => 'Em gái', 'phien_am' => 'Em gái', 'cap_do' => 'de', 'img' => "$u/photo-1503454537688-e47440ddd11d?$q"],
                ['tu_chuan' => 'Chú bác', 'phien_am' => 'Chú bác', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1520909385260-470d80ce6470?$q"],
                ['tu_chuan' => 'Cô dì', 'phien_am' => 'Cô dì', 'cap_do' => 'de', 'img' => "$u/photo-1516116216624-53fc36295bb9?$q"],
                ['tu_chuan' => 'Cháu gái', 'phien_am' => 'Cháu gái', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1503454537688-e47440ddd11d?$q"],
                ['tu_chuan' => 'Người yêu', 'phien_am' => 'Người yêu', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1516251193007-14233e92747d?$q"],
                ['tu_chuan' => 'Gia đình', 'phien_am' => 'Gia đình', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1503454537688-e47440ddd11d?$q"],
            ],
            'Từ vựng: Thế giới động vật' => [
                ['tu_chuan' => 'Con sư tử', 'phien_am' => 'Con sư tử', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1614027164847-1b28cfe1df60?$q"],
                ['tu_chuan' => 'Con voi', 'phien_am' => 'Con voi', 'cap_do' => 'de', 'img' => "$u/photo-1628840042765-356cda07f423?$q"],
                ['tu_chuan' => 'Con gấu', 'phien_am' => 'Con gấu', 'cap_do' => 'de', 'img' => "$u/photo-1618575239108-cb42b51a5f21?$q"],
                ['tu_chuan' => 'Con khỉ', 'phien_am' => 'Con khỉ', 'cap_do' => 'de', 'img' => "$u/photo-1634191704198-eb10f9f73dbc?$q"],
                ['tu_chuan' => 'Con hươu', 'phien_am' => 'Con hươu', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1585110396000-c9ffd4e4b308?$q"],
                ['tu_chuan' => 'Con ngựa', 'phien_am' => 'Con ngựa', 'cap_do' => 'de', 'img' => "$u/photo-1633634702241-25f816babed0?$q"],
                ['tu_chuan' => 'Con chó sói', 'phien_am' => 'Con chó sói', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1560807707-38cc612d5f38?$q"],
                ['tu_chuan' => 'Con báo', 'phien_am' => 'Con báo', 'cap_do' => 'de', 'img' => "$u/photo-1574304439104-e87b1f44c7fe?$q"],
                ['tu_chuan' => 'Con tê giác', 'phien_am' => 'Con tê giác', 'cap_do' => 'kho', 'img' => "$u/photo-1583511655857-d19db992cb74?$q"],
                ['tu_chuan' => 'Con hà mã', 'phien_am' => 'Con hà mã', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1612623414376-fc950c4d9f1b?$q"],
                ['tu_chuan' => 'Con cá sấu', 'phien_am' => 'Con cá sấu', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1570141961143-693630623a48?$q"],
                ['tu_chuan' => 'Con đà điểu', 'phien_am' => 'Con đà điểu', 'cap_do' => 'kho', 'img' => "$u/photo-1559827260-dc66d52bef19?$q"],
                ['tu_chuan' => 'Con heo', 'phien_am' => 'Con heo', 'cap_do' => 'de', 'img' => "$u/photo-1590072395245-84d9ccaa3bcd?$q"],
                ['tu_chuan' => 'Con bò', 'phien_am' => 'Con bò', 'cap_do' => 'de', 'img' => "$u/photo-1509042239860-f550ce710b93?$q"],
                ['tu_chuan' => 'Con dê', 'phien_am' => 'Con dê', 'cap_do' => 'de', 'img' => "$u/photo-1596854407944-bf87f6fdd49e?$q"],
            ],
            'Từ vựng: Thiên nhiên và mùa' => [
                ['tu_chuan' => 'Mùa xuân', 'phien_am' => 'Mùa xuân', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Mùa hè', 'phien_am' => 'Mùa hè', 'cap_do' => 'de', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Mùa thu', 'phien_am' => 'Mùa thu', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Mùa đông', 'phien_am' => 'Mùa đông', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1514288286060-460e85c6c28b?$q"],
                ['tu_chuan' => 'Cây xanh', 'phien_am' => 'Cây xanh', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1518531933037-91b2f8894cc0?$q"],
                ['tu_chuan' => 'Hoa hồng', 'phien_am' => 'Hoa hồng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1520763185298-1b434c919afe?$q"],
                ['tu_chuan' => 'Trời nắng', 'phien_am' => 'Trời nắng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1495567720989-cebdbdd97913?$q"],
                ['tu_chuan' => 'Mây trắng', 'phien_am' => 'Mây trắng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1495567720989-cebdbdd97913?$q"],
                ['tu_chuan' => 'Mưa rơi', 'phien_am' => 'Mưa rơi', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1512521776298-002867215603?$q"],
                ['tu_chuan' => 'Gió bay', 'phien_am' => 'Gió bay', 'cap_do' => 'de', 'img' => "$u/photo-1506905925346-21bda4d32df4?$q"],
                ['tu_chuan' => 'Tuyết trắng', 'phien_am' => 'Tuyết trắng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1514888286974-6c03e2ca1dba?$q"],
                ['tu_chuan' => 'Sao sáng', 'phien_am' => 'Sao sáng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1419242902214-272b3f66ee7a?$q"],
                ['tu_chuan' => 'Mặt trăng', 'phien_am' => 'Mặt trăng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1419242902214-272b3f66ee7a?$q"],
                ['tu_chuan' => 'Cầu vồng', 'phien_am' => 'Cầu vồng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1509114397686-ac8e9a320a95?$q"],
                ['tu_chuan' => 'Biển sóng', 'phien_am' => 'Biển sóng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1505142468610-359e7d316be0?$q"],
            ],
            'Từ vựng: Trái cây miền nhiệt đới' => [
                ['tu_chuan' => 'Xoài cát', 'phien_am' => 'Xoài cát', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Sầu riêng', 'phien_am' => 'Sầu riêng', 'cap_do' => 'kho', 'img' => "$u/photo-1585074269193-40141e90fa25?$q"],
                ['tu_chuan' => 'Măng cụt', 'phien_am' => 'Măng cụt', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Vú sữa', 'phien_am' => 'Vú sữa', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1585074269193-40141e90fa25?$q"],
                ['tu_chuan' => 'Nhãn lồng', 'phien_am' => 'Nhãn lồng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Vải thiều', 'phien_am' => 'Vải thiều', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1585074269193-40141e90fa25?$q"],
                ['tu_chuan' => 'Chuối vàng', 'phien_am' => 'Chuối vàng', 'cap_do' => 'de', 'img' => "$u/photo-1571771894821-ce9b6c11b08e?$q"],
                ['tu_chuan' => 'Dứa ngoại', 'phien_am' => 'Dứa ngoại', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Ổi hồng', 'phien_am' => 'Ổi hồng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1585074269193-40141e90fa25?$q"],
                ['tu_chuan' => 'Chanh leo', 'phien_am' => 'Chanh leo', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
                ['tu_chuan' => 'Dâu tây', 'phien_am' => 'Dâu tây', 'cap_do' => 'de', 'img' => "$u/photo-1585074269193-40141e90fa25?$q"],
                ['tu_chuan' => 'Lựu đỏ', 'phien_am' => 'Lựu đỏ', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1599599810694-b5ac4dd4eae9?$q"],
            ],
            'Từ vựng: Các loài hoa' => [
                ['tu_chuan' => 'Hoa hồng', 'phien_am' => 'Hoa hồng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1520763185298-1b434c919afe?$q"],
                ['tu_chuan' => 'Hoa cúc', 'phien_am' => 'Hoa cúc', 'cap_do' => 'de', 'img' => "$u/photo-1513364776144-60967b0f800f?$q"],
                ['tu_chuan' => 'Hoa mai', 'phien_am' => 'Hoa mai', 'cap_do' => 'de', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa đào', 'phien_am' => 'Hoa đào', 'cap_do' => 'de', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa sen', 'phien_am' => 'Hoa sen', 'cap_do' => 'de', 'img' => "$u/photo-1578431494494-da8e0ecc0355?$q"],
                ['tu_chuan' => 'Hoa huệ', 'phien_am' => 'Hoa huệ', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa lan', 'phien_am' => 'Hoa lan', 'cap_do' => 'de', 'img' => "$u/photo-1518895949257-7621c3c786d7?$q"],
                ['tu_chuan' => 'Hoa lily', 'phien_am' => 'Hoa lily', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1520763185298-1b434c919afe?$q"],
                ['tu_chuan' => 'Hoa tulip', 'phien_am' => 'Hoa tulip', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1519046904884-53103b34b206?$q"],
                ['tu_chuan' => 'Hoa thược dược', 'phien_am' => 'Hoa thược dược', 'cap_do' => 'kho', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa xương rồng', 'phien_am' => 'Hoa xương rồng', 'cap_do' => 'kho', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa cẩm chướng', 'phien_am' => 'Hoa cẩm chướng', 'cap_do' => 'kho', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
                ['tu_chuan' => 'Hoa bưởi', 'phien_am' => 'Hoa bưởi', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1490147868817-67d3d2b3f19b?$q"],
            ],
            'Các bộ phận cơ thể' => [
                ['tu_chuan' => 'Đôi mắt', 'phien_am' => 'Đôi mắt', 'cap_do' => 'de', 'img' => "$u/photo-1544435232-948266493d90?$q"],
                ['tu_chuan' => 'Cái miệng', 'phien_am' => 'Cái miệng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1506461883276-594a12b11cf3?$q"],
                ['tu_chuan' => 'Bàn tay', 'phien_am' => 'Bàn tay', 'cap_do' => 'de', 'img' => "$u/photo-1510674485131-dc88d9809fa0?$q"],
                ['tu_chuan' => 'Cái lưỡi', 'phien_am' => 'Cái lưỡi', 'cap_do' => 'kho', 'img' => "$u/photo-1502444330042-d1a1ddf9bb5c?$q"],
                ['tu_chuan' => 'Cái mũi', 'phien_am' => 'Cái mũi', 'cap_do' => 'de', 'img' => "$u/photo-1532321592770-a27ff7874f59?$q"],
                ['tu_chuan' => 'Hai tai', 'phien_am' => 'Hai tai', 'cap_do' => 'de', 'img' => "$u/photo-1615886287662-1c45f0a5621e?$q"],
                ['tu_chuan' => 'Mái tóc', 'phien_am' => 'Mái tóc', 'cap_do' => 'de', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Cái đầu', 'phien_am' => 'Cái đầu', 'cap_do' => 'de', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Cái cơ thể', 'phien_am' => 'Cái cơ thể', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1534438327276-14e5300c3a48?$q"],
                ['tu_chuan' => 'Bàn chân', 'phien_am' => 'Bàn chân', 'cap_do' => 'de', 'img' => "$u/photo-1519046904884-53103b34b206?$q"],
                ['tu_chuan' => 'Cái bắp tay', 'phien_am' => 'Cái bắp tay', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1534438327276-14e5300c3a48?$q"],
                ['tu_chuan' => 'Đầu gối', 'phien_am' => 'Đầu gối', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1534438327276-14e5300c3a48?$q"],
                ['tu_chuan' => 'Cổ họng', 'phien_am' => 'Cổ họng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1506461883276-594a12b11cf3?$q"],
                ['tu_chuan' => 'Bụng', 'phien_am' => 'Bụng', 'cap_do' => 'de', 'img' => "$u/photo-1534438327276-14e5300c3a48?$q"],
                ['tu_chuan' => 'Lưng', 'phien_am' => 'Lưng', 'cap_do' => 'de', 'img' => "$u/photo-1534438327276-14e5300c3a48?$q"],
            ],
            'Thế giới đại dương' => [
                ['tu_chuan' => 'Con sứa', 'phien_am' => 'Con sứa', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1544710328-41735f1c3907?$q"],
                ['tu_chuan' => 'San hô', 'phien_am' => 'San hô', 'cap_do' => 'de', 'img' => "$u/photo-1546026423-cc0643f54970?$q"],
                ['tu_chuan' => 'Sao biển', 'phien_am' => 'Sao biển', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1550503990-2ee2da4a0eb5?$q"],
                ['tu_chuan' => 'Cá voi', 'phien_am' => 'Cá voi', 'cap_do' => 'de', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
                ['tu_chuan' => 'Cá heo', 'phien_am' => 'Cá heo', 'cap_do' => 'de', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
                ['tu_chuan' => 'Bạch tuộc', 'phien_am' => 'Bạch tuộc', 'cap_do' => 'kho', 'img' => "$u/photo-1559827260-dc66d52bef19?$q"],
                ['tu_chuan' => 'Tôm hùm', 'phien_am' => 'Tôm hùm', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1546026423-cc0643f54970?$q"],
                ['tu_chuan' => 'Cua cà', 'phien_am' => 'Cua cà', 'cap_do' => 'de', 'img' => "$u/photo-1511689915389-ee4dc8151032?$q"],
                ['tu_chuan' => 'Ốc sò', 'phien_am' => 'Ốc sò', 'cap_do' => 'de', 'img' => "$u/photo-1546026423-cc0643f54970?$q"],
                ['tu_chuan' => 'Cá mập', 'phien_am' => 'Cá mập', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
                ['tu_chuan' => 'Cá ngựa', 'phien_am' => 'Cá ngựa', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
                ['tu_chuan' => 'Cá chép', 'phien_am' => 'Cá chép', 'cap_do' => 'de', 'img' => "$u/photo-1568430462989-44163eb1752f?$q"],
            ],
            'Phán biệt âm S và X' => [
                ['tu_chuan' => 'Phù sa', 'phien_am' => 'Phù sa', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1534400263227-aa4317816c11?$q"],
                ['tu_chuan' => 'Xa xôi', 'phien_am' => 'Xa xôi', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Người sinh', 'phien_am' => 'Người sinh', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Xin sáng', 'phien_am' => 'Xin sáng', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1495567720989-cebdbdd97913?$q"],
                ['tu_chuan' => 'Sự xử', 'phien_am' => 'Sự xử', 'cap_do' => 'kho', 'img' => "$u/photo-1507003211169-0a1dd7228f2d?$q"],
                ['tu_chuan' => 'Sống xã', 'phien_am' => 'Sống xã', 'cap_do' => 'trung_binh', 'img' => "$u/photo-1511689915389-ee4dc8151032?$q"],
            ],
        ];

        foreach ($catalog as $tieuDe => $mangTu) {
            $bhId = DB::table('bai_hocs')->where('tieu_de', $tieuDe)->value('id');

            if (!$bhId) {
                continue;
            }

            $thuTu = 0;
            foreach ($mangTu as $row) {
                $thuTu++;
                DB::table('tu_vungs')->updateOrInsert(
                    [
                        'bai_hoc_id' => $bhId,
                        'tu_chuan' => $row['tu_chuan'],
                    ],
                    [
                        'phien_am' => $row['phien_am'],
                        'cap_do' => $row['cap_do'],
                        'hinh_anh_url' => $row['img'],
                        'am_thanh_mau_url' => null, 
                        'thu_tu' => $thuTu,
                    ]
                );
            }
        }
    }
}
