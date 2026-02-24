-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 22. Jan 2026 um 11:40
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `sport_shop`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Addresses`
--

CREATE TABLE `Addresses` (
                             `address_id` int(11) NOT NULL,
                             `zip_code` varchar(10) NOT NULL,
                             `house_number` varchar(10) NOT NULL,
                             `street` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Addresses`
--

INSERT INTO `Addresses` (`address_id`, `zip_code`, `house_number`, `street`) VALUES
                                                                                 (1, '10115', '42', 'Unter den Linden'),
                                                                                 (2, '80331', '15', 'Marienplatz'),
                                                                                 (3, '12345', '123', 'Teststraße'),
                                                                                 (4, '50667', '23', 'Hohe Straße'),
                                                                                 (5, '60311', '8', 'Zeil'),
                                                                                 (6, '99999', '99', 'Musterweg'),
                                                                                 (7, '70173', '12', 'Königstraße'),
                                                                                 (8, '01067', '5', 'Prager Straße'),
                                                                                 (9, '77777', '77', 'Beispielstraße'),
                                                                                 (10, '04109', '18', 'Petersstraße'),
                                                                                 (11, '10115', '1', 'Adminstraße'),
                                                                                 (16, 'ad', 'ad', 'sad'),
                                                                                 (17, '10115', '1', 'Adminstraße'),
                                                                                 (18, '10115', '1', 'Adminstraße'),
                                                                                 (19, '99054', 'muster', 'muster');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Cart`
--

CREATE TABLE `Cart` (
                        `user_id` int(11) NOT NULL,
                        `product_id` int(11) NOT NULL,
                        `product_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Cart`
--

INSERT INTO `Cart` (`user_id`, `product_id`, `product_amount`) VALUES
                                                                   (1, 6, 1),
                                                                   (1, 22, 2),
                                                                   (2, 11, 1),
                                                                   (3, 7, 1),
                                                                   (4, 15, 3),
                                                                   (6, 13, 5),
                                                                   (6, 18, 2),
                                                                   (9, 5, 1),
                                                                   (10, 8, 1),
                                                                   (10, 19, 1),
                                                                   (16, 7, 1),
                                                                   (16, 12, 1),
                                                                   (16, 13, 2),
                                                                   (16, 15, 2),
                                                                   (16, 16, 2),
                                                                   (16, 23, 6);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Categories`
--

CREATE TABLE `Categories` (
                              `category_id` int(11) NOT NULL,
                              `category_name` varchar(100) NOT NULL,
                              `asset_path` varchar(255) DEFAULT NULL,
                              `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Categories`
--

INSERT INTO `Categories` (`category_id`, `category_name`, `asset_path`, `description`) VALUES
                                                                                           (1, 'Proteinpulver', 'assets/images/Test_Bild-removebg-preview.png', 'Hochwertiges Eiweiß für deinen Muskelaufbau und Regeneration.'),
                                                                                           (2, 'Performance', 'assets/images/Test_Bild-removebg-preview.png', 'Booster und Supplements zur Steigerung deiner Trainingsleistung.'),
                                                                                           (3, 'Bars & Snacks', 'assets/images/Test_Bild-removebg-preview.png', 'Gesunde Snacks und Proteinriegel für zwischendurch.'),
                                                                                           (4, 'Vitalstoffe', 'assets/images/Test_Bild-removebg-preview.png', 'Vitamine und Mineralien für deine allgemeine Gesundheit und Vitalität.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `OrderItems`
--

CREATE TABLE `OrderItems` (
                              `order_id` int(11) NOT NULL,
                              `product_id` int(11) NOT NULL,
                              `product_amount` int(11) NOT NULL,
                              `order_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `OrderItems`
--

INSERT INTO `OrderItems` (`order_id`, `product_id`, `product_amount`, `order_total`) VALUES
                                                                                         (1, 7, 1, 19.99),
                                                                                         (2, 13, 10, 24.90),
                                                                                         (2, 19, 2, 33.98),
                                                                                         (3, 3, 1, 29.99),
                                                                                         (3, 15, 5, 14.95),
                                                                                         (4, 8, 1, 24.99),
                                                                                         (4, 11, 2, 37.98),
                                                                                         (5, 2, 1, 29.99),
                                                                                         (5, 20, 1, 19.99),
                                                                                         (5, 23, 1, 12.99),
                                                                                         (6, 14, 3, 11.97),
                                                                                         (6, 16, 1, 9.99),
                                                                                         (7, 5, 1, 27.99),
                                                                                         (7, 9, 1, 22.99),
                                                                                         (8, 17, 1, 16.99),
                                                                                         (8, 21, 1, 14.99),
                                                                                         (9, 12, 5, 12.45),
                                                                                         (9, 18, 1, 9.99),
                                                                                         (10, 4, 1, 34.99),
                                                                                         (10, 10, 1, 26.99),
                                                                                         (11, 7, 10, 190.00),
                                                                                         (11, 12, 1, 2.00),
                                                                                         (11, 13, 1, 2.00),
                                                                                         (11, 23, 1, 9.00),
                                                                                         (12, 14, 2, 6.00),
                                                                                         (12, 15, 1, 5.00),
                                                                                         (12, 16, 1, 2.00),
                                                                                         (13, 20, 1, 14.00),
                                                                                         (14, 8, 1, 24.00),
                                                                                         (14, 9, 1, 22.00),
                                                                                         (14, 10, 1, 26.00),
                                                                                         (15, 12, 1, 2.00),
                                                                                         (16, 14, 1, 3.00),
                                                                                         (17, 20, 1, 14.00),
                                                                                         (17, 21, 1, 12.00),
                                                                                         (17, 23, 1, 9.00),
                                                                                         (18, 13, 1, 2.00),
                                                                                         (18, 14, 1, 3.00),
                                                                                         (19, 2, 1, 29.00),
                                                                                         (19, 3, 1, 29.00),
                                                                                         (19, 4, 1, 34.00);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Orders`
--

CREATE TABLE `Orders` (
                          `order_id` int(11) NOT NULL,
                          `order_address_id` int(11) NOT NULL,
                          `first_name` varchar(50) DEFAULT NULL,
                          `last_name` varchar(50) DEFAULT NULL,
                          `guest_email` varchar(100) DEFAULT NULL,
                          `user_id` int(11) DEFAULT NULL,
                          `date` datetime NOT NULL DEFAULT current_timestamp(),
                          `state` varchar(20) NOT NULL DEFAULT 'pending' CHECK (`state` in ('pending','paid','shipped','closed','canceled'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Orders`
--

INSERT INTO `Orders` (`order_id`, `order_address_id`, `first_name`, `last_name`, `guest_email`, `user_id`, `date`, `state`) VALUES
                                                                                                                                (1, 1, 'Max', 'Mustermann', NULL, 1, '2024-11-15 10:30:00', 'closed'),
                                                                                                                                (2, 2, 'Anna', 'Schmidt', NULL, 2, '2024-11-20 14:15:00', 'shipped'),
                                                                                                                                (3, 3, 'Gast', 'User', 'gast@email.de', NULL, '2024-12-01 09:00:00', 'paid'),
                                                                                                                                (4, 4, 'Lisa', 'Becker', NULL, 4, '2024-12-05 16:45:00', 'pending'),
                                                                                                                                (5, 5, 'Thomas', 'Fischer', NULL, 5, '2024-12-08 11:20:00', 'shipped'),
                                                                                                                                (6, 6, 'Gast', 'Besteller', 'test.gast@email.de', NULL, '2024-12-10 13:30:00', 'closed'),
                                                                                                                                (7, 7, 'Julia', 'Meyer', NULL, 6, '2024-12-12 15:00:00', 'paid'),
                                                                                                                                (8, 8, 'Michael', 'Weber', NULL, 7, '2024-12-14 10:10:00', 'pending'),
                                                                                                                                (9, 9, 'Gast', 'Kunde', 'kunde@email.de', NULL, '2024-12-15 12:00:00', 'shipped'),
                                                                                                                                (10, 10, 'Sarah', 'Schulz', NULL, 8, '2024-12-16 14:30:00', 'paid'),
                                                                                                                                (11, 11, 'e', 'e', 'e@e', NULL, '2026-01-08 22:10:46', 'pending'),
                                                                                                                                (12, 12, 'a', 'asd', 'e@e.com', NULL, '2026-01-13 15:02:29', 'pending'),
                                                                                                                                (13, 13, 'a', 'asd', 'e@e.com', NULL, '2026-01-13 15:07:14', 'pending'),
                                                                                                                                (14, 14, 'e@e.com', 'w', 'e@e.com', NULL, '2026-01-13 15:12:03', 'pending'),
                                                                                                                                (15, 15, 'e', 'e', 'e@e.com', NULL, '2026-01-13 15:13:35', 'pending'),
                                                                                                                                (16, 16, 'e', 'e', 'e@e.com', NULL, '2026-01-13 16:16:00', 'pending'),
                                                                                                                                (17, 17, 'emre', 'koc', 'e@e.com', 16, '2026-01-13 16:28:44', 'pending'),
                                                                                                                                (18, 18, 'emre', 'koc', 'e@e.com', 16, '2026-01-13 16:33:11', 'pending'),
                                                                                                                                (19, 19, 'emre', 'koc', 'e@e.com', 16, '2026-01-13 16:37:40', 'pending');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Order_Addresses`
--

CREATE TABLE `Order_Addresses` (
                                   `address_id` int(11) NOT NULL,
                                   `zip_code` varchar(10) NOT NULL,
                                   `house_number` varchar(10) NOT NULL,
                                   `street` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Order_Addresses`
--

INSERT INTO `Order_Addresses` (`address_id`, `zip_code`, `house_number`, `street`) VALUES
                                                                                       (1, '10115', '42', 'Unter den Linden'),
                                                                                       (2, '80331', '15', 'Marienplatz'),
                                                                                       (3, '12345', '123', 'Teststraße'),
                                                                                       (4, '50667', '23', 'Hohe Straße'),
                                                                                       (5, '60311', '8', 'Zeil'),
                                                                                       (6, '99999', '99', 'Musterweg'),
                                                                                       (7, '70173', '12', 'Königstraße'),
                                                                                       (8, '01067', '5', 'Prager Straße'),
                                                                                       (9, '77777', '77', 'Beispielstraße'),
                                                                                       (10, '04109', '18', 'Petersstraße'),
                                                                                       (11, 'sd', 'as', 's'),
                                                                                       (12, 'awd', 'we', 'asd'),
                                                                                       (13, 'yxcy', 'yxcy', 'xvy'),
                                                                                       (14, 'yxc', 'ycx', 'cy'),
                                                                                       (15, 'xcy', 'ycyx', 'ycyxc'),
                                                                                       (16, '<yc', 'y<yc', '<yc'),
                                                                                       (17, 'yxc', 'yxc', 'yxc'),
                                                                                       (18, 'dsa', 'asd', 'asd'),
                                                                                       (19, 'yxc', 'ycx', 'sccy');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_resets`
--

CREATE TABLE `password_resets` (
                                   `reset_id` int(11) NOT NULL,
                                   `user_id` int(11) NOT NULL,
                                   `token` varchar(64) NOT NULL,
                                   `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Permissions`
--

CREATE TABLE `Permissions` (
                               `permission_id` int(11) NOT NULL,
                               `permission_key` varchar(100) NOT NULL,
                               `permission_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Permissions`
--

INSERT INTO `Permissions` (`permission_id`, `permission_key`, `permission_description`) VALUES
                                                                                            (1, 'product.read', 'Produkte ansehen'),
                                                                                            (2, 'product.create', 'Produkte anlegen'),
                                                                                            (3, 'product.update', 'Produkte bearbeiten'),
                                                                                            (4, 'product.delete', 'Produkte löschen'),
                                                                                            (5, 'category.manage', 'Kategorien verwalten'),
                                                                                            (6, 'user.read', 'Benutzer ansehen'),
                                                                                            (7, 'user.create', 'Benutzer anlegen'),
                                                                                            (8, 'user.update', 'Benutzer bearbeiten'),
                                                                                            (9, 'user.delete', 'Benutzer löschen'),
                                                                                            (10, 'order.read', 'Bestellungen ansehen'),
                                                                                            (11, 'order.update_state', 'Bestellstatus ändern'),
                                                                                            (12, 'cart.manage', 'Warenkorb verwalten'),
                                                                                            (13, 'rating.create', 'Bewertungen erstellen'),
                                                                                            (14, 'role.assign', 'Rollen zuweisen'),
                                                                                            (15, 'system.admin', 'Alle Rechte');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Products`
--

CREATE TABLE `Products` (
                            `product_id` int(11) NOT NULL,
                            `product_name` varchar(200) NOT NULL,
                            `price` decimal(10,2) NOT NULL,
                            `category_id` int(11) NOT NULL,
                            `inventory` int(11) NOT NULL DEFAULT 0,
                            `units_sold` int(11) NOT NULL DEFAULT 0,
                            `flavour` varchar(50) DEFAULT NULL,
                            `size` varchar(50) DEFAULT NULL,
                            `description_short` varchar(500) DEFAULT NULL,
                            `description_long` text DEFAULT NULL,
                            `asset_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Products`
--

INSERT INTO `Products` (`product_id`, `product_name`, `price`, `category_id`, `inventory`, `units_sold`, `flavour`, `size`, `description_short`, `description_long`, `asset_path`) VALUES
                                                                                                                                                                                       (2, 'Whey Protein Premium', 29.99, 1, 119, 66, 'Vanille', '1000g', 'Hochwertiges Whey Protein mit 80% Proteingehalt', 'Unser Premium Whey Protein ist die perfekte Wahl für den Muskelaufbau. Mit 80% Proteingehalt und hervorragender Löslichkeit unterstützt es deine Fitnessziele optimal. Reich an BCAAs und schnell verfügbar nach dem Training.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (3, 'Whey Protein Premium', 29.99, 1, 94, 55, 'Erdbeere', '1000g', 'Hochwertiges Whey Protein mit 80% Proteingehalt', 'Unser Premium Whey Protein ist die perfekte Wahl für den Muskelaufbau. Mit 80% Proteingehalt und hervorragender Löslichkeit unterstützt es deine Fitnessziele optimal. Reich an BCAAs und schnell verfügbar nach dem Training.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (4, 'Casein Protein', 34.99, 1, 79, 43, 'Schokolade', '900g', 'Langsam verdauliches Nacht-Protein', 'Casein Protein ist ideal für die Nacht oder lange Phasen ohne Nahrungsaufnahme. Es versorgt deine Muskeln über Stunden hinweg kontinuierlich mit Aminosäuren und unterstützt die Regeneration während des Schlafs.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (5, 'Vegan Protein Mix', 27.99, 1, 110, 38, 'Schoko-Nuss', '750g', 'Pflanzliches Mehrkomponenten-Protein', 'Unser veganes Protein kombiniert Erbsen-, Reis- und Hanfprotein für ein vollständiges Aminosäureprofil. Perfekt für pflanzliche Ernährung ohne Kompromisse bei der Qualität. Laktose- und glutenfrei.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (6, 'Isolat Protein 95', 39.99, 1, 65, 29, 'Neutral', '800g', '95% reines Protein-Isolat', 'Das reinste Protein in unserem Sortiment. Mit 95% Proteingehalt, nahezu frei von Fett und Kohlenhydraten. Ideal für Diätphasen und höchste Ansprüche an Reinheit.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (7, 'Kreatin Monohydrat', 19.99, 2, 190, 166, NULL, '500g', 'Reines Kreatin für mehr Kraft und Ausdauer', 'Kreatin Monohydrat ist eines der am besten erforschten Supplements. Es verbessert die Kraftleistung, unterstützt den Muskelaufbau und verkürzt die Regenerationszeit. 100% rein, Creapure Qualität.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (8, 'Pre-Workout Booster', 24.99, 2, 129, 99, 'Tropical Punch', '300g', 'Energie und Focus für intensive Workouts', 'Unser Pre-Workout Booster kombiniert Koffein, Beta-Alanin und Citrullin für maximale Leistung. Erhöhte Energie, besserer Focus und intensivere Pumps für dein bestes Training.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (9, 'BCAA 2:1:1', 22.99, 2, 144, 77, 'Zitrone', '400g', 'Essentielle Aminosäuren für Muskelschutz', 'BCAAs im optimalen 2:1:1 Verhältnis schützen deine Muskeln während intensiver Trainingseinheiten und unterstützen die Regeneration. Perfekt für Cardio und Krafttraining.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (10, 'EAA Complex', 26.99, 2, 89, 52, 'Wassermelone', '450g', 'Alle essentiellen Aminosäuren', 'EAAs enthalten alle 9 essentiellen Aminosäuren für optimale Proteinsynthese. Ideal während und nach dem Training für maximalen Muskelaufbau und schnellere Erholung.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (11, 'Beta-Alanin', 18.99, 2, 105, 44, NULL, '300g', 'Für mehr Ausdauer und weniger Ermüdung', 'Beta-Alanin erhöht den Carnosin-Spiegel in der Muskulatur und verzögert die Übersäuerung. Das Ergebnis: Mehr Wiederholungen, längere Sätze und bessere Leistung.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (12, 'Protein Riegel Schoko', 2.49, 3, 298, 247, 'Schokolade', '60g', '20g Protein, wenig Zucker', 'Perfekter Snack für unterwegs mit 20g hochwertigem Protein. Schokoladiger Geschmack ohne schlechtes Gewissen. Nur 2g Zucker und reich an Ballaststoffen.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (13, 'Protein Riegel Karamell', 2.49, 3, 278, 200, 'Karamell', '60g', '20g Protein, wenig Zucker', 'Cremiger Karamell-Genuss mit 20g Protein. Ideal als Zwischenmahlzeit oder Post-Workout Snack. Glutenfrei und mit natürlichen Aromen.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (14, 'Protein Cookies', 3.99, 3, 176, 136, 'Chocolate Chip', '75g', 'Weiche Protein-Cookies mit 15g Protein', 'Saftige Cookies mit echten Schoko-Chips und 15g Protein pro Cookie. Der perfekte Süß-Snack für Fitness-Bewusste. Ohne künstliche Süßstoffe.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (15, 'Nuss-Mix Premium', 5.99, 3, 149, 90, 'Mixed Nuts', '200g', 'Hochwertige Nüsse und Kerne', 'Premium Mischung aus Mandeln, Cashews, Walnüssen und Paranüssen. Reich an gesunden Fetten, Proteinen und Mikronährstoffen. Perfekter Energie-Snack.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (16, 'Protein Chips Paprika', 2.99, 3, 219, 157, 'Paprika', '50g', 'Knusprige Chips mit 21g Protein', 'Revolutionäre Protein-Chips mit 40% weniger Fett als normale Chips. 21g Protein pro Packung und intensiver Paprika-Geschmack. Der gesunde Snack für Chip-Liebhaber.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (17, 'Protein Pancake Mix', 9.99, 3, 95, 67, 'Neutral', '500g', 'Einfache Zubereitung, 35g Protein pro Portion', 'Protein-Pancakes in Minuten zubereitet. Einfach Wasser hinzufügen, fertig! 35g Protein pro Portion und fluffig-leckerer Geschmack. Perfektes Fitness-Frühstück.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (18, 'Multivitamin Komplex', 16.99, 4, 175, 123, NULL, '90 Kapseln', 'Alle wichtigen Vitamine und Mineralien', 'Unser Multivitamin deckt den Tagesbedarf aller wichtigen Vitamine und Mineralien. Optimal abgestimmt für aktive Menschen. Eine Kapsel täglich für vollständige Versorgung.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (19, 'Omega-3 Fischöl', 19.99, 4, 140, 98, NULL, '120 Kapseln', 'EPA und DHA für Herz und Gehirn', 'Hochdosiertes Omega-3 aus nachhaltigem Fischfang. 1000mg EPA/DHA pro Portion für Herzgesundheit, Gehirnfunktion und Entzündungshemmung. Molekular destilliert für höchste Reinheit.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (20, 'Vitamin D3 + K2', 14.99, 4, 158, 114, NULL, '100 Tabletten', 'Für Knochen und Immunsystem', 'Die perfekte Kombination aus Vitamin D3 (5000 IE) und K2 (200µg). Unterstützt Knochengesundheit, Immunsystem und Calcium-Stoffwechsel. Besonders wichtig in den Wintermonaten.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (21, 'Magnesium Citrat', 12.99, 4, 189, 146, NULL, '120 Kapseln', 'Hochverfügbares Magnesium', 'Magnesiumcitrat mit hoher Bioverfügbarkeit. Unterstützt Muskel- und Nervenfunktion, reduziert Müdigkeit und beugt Krämpfen vor. 400mg elementares Magnesium pro Portion.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (22, 'Zink + Selen', 11.99, 4, 200, 87, NULL, '100 Tabletten', 'Für Immunsystem und Zellschutz', 'Optimale Kombination aus Zink (25mg) und Selen (100µg). Stärkt das Immunsystem, schützt Zellen vor oxidativem Stress und unterstützt Haar, Haut und Nägel.', 'assets/images/Test_Bild-removebg-preview.png'),
                                                                                                                                                                                       (23, 'Vitamin C 1000mg', 9.99, 4, 208, 169, NULL, '100 Tabletten', 'Hochdosiertes Vitamin C mit Langzeitwirkung', 'Time-Release Vitamin C Tabletten mit 1000mg pro Portion. Unterstützt das Immunsystem über den ganzen Tag verteilt. Mit Hagebuttenextrakt für bessere Aufnahme.', 'assets/images/Test_Bild-removebg-preview.png');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Ratings`
--

CREATE TABLE `Ratings` (
                           `rating_id` int(11) NOT NULL,
                           `product_id` int(11) NOT NULL,
                           `user_id` int(11) NOT NULL,
                           `rating_score` int(11) NOT NULL,
                           `comment` text DEFAULT NULL,
                           `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Ratings`
--

INSERT INTO `Ratings` (`rating_id`, `product_id`, `user_id`, `rating_score`, `comment`, `date`) VALUES
                                                                                                    (3, 2, 4, 5, 'Vanille-Geschmack ist perfekt, nicht zu süß. Sehr zufrieden!', '2024-11-22 10:15:00'),
                                                                                                    (4, 3, 6, 4, 'Leckerer Erdbeergeschmack, gute Löslichkeit.', '2024-12-01 16:00:00'),
                                                                                                    (5, 7, 1, 5, 'Kreatin wie es sein soll. Keine Nebenwirkungen, gute Wirkung.', '2024-11-17 11:00:00'),
                                                                                                    (6, 7, 5, 5, 'Bestes Kreatin das ich je hatte. Creapure Qualität merkt man!', '2024-12-09 13:00:00'),
                                                                                                    (7, 8, 7, 4, 'Guter Booster, gibt ordentlich Power. Eine Stunde vorher nehmen!', '2024-12-15 15:30:00'),
                                                                                                    (8, 13, 2, 5, 'Beste Proteinriegel ever! Schmeckt wie echter Schokoriegel.', '2024-11-21 12:00:00'),
                                                                                                    (9, 13, 8, 5, 'Perfekt für unterwegs. Schmeckt gut und macht satt.', '2024-12-17 10:00:00'),
                                                                                                    (10, 14, 4, 4, 'Karamell-Geschmack ist lecker, Konsistenz könnte weicher sein.', '2024-12-06 14:00:00'),
                                                                                                    (11, 17, 1, 5, 'Nehme ich täglich. Fühle mich fitter und gesünder!', '2024-11-19 09:30:00'),
                                                                                                    (12, 19, 5, 5, 'Vitamin D3 + K2 Kombi ist perfekt. Gerade im Winter unverzichtbar.', '2024-12-10 11:00:00'),
                                                                                                    (13, 20, 6, 4, 'Hilft gut gegen Muskelkrämpfe. Nehme es nach dem Training.', '2024-12-13 16:30:00'),
                                                                                                    (14, 5, 9, 5, 'Als Veganer sehr zufrieden! Schmeckt gut und keine Verdauungsprobleme.', '2024-12-16 13:00:00'),
                                                                                                    (15, 9, 7, 4, 'BCAAs schmecken gut und helfen bei der Regeneration.', '2024-12-15 17:00:00'),
                                                                                                    (21, 7, 16, 5, 'toll', '2026-01-13 17:04:48');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Roles`
--

CREATE TABLE `Roles` (
                         `role_id` int(11) NOT NULL,
                         `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Roles`
--

INSERT INTO `Roles` (`role_id`, `role_name`) VALUES
                                                 (4, 'admin'),
                                                 (2, 'product_manager'),
                                                 (1, 'user'),
                                                 (3, 'user_manager');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Role_Permissions`
--

CREATE TABLE `Role_Permissions` (
                                    `role_id` int(11) NOT NULL,
                                    `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Role_Permissions`
--

INSERT INTO `Role_Permissions` (`role_id`, `permission_id`) VALUES
                                                                (1, 1),
                                                                (1, 10),
                                                                (1, 12),
                                                                (1, 13),
                                                                (2, 1),
                                                                (2, 2),
                                                                (2, 3),
                                                                (2, 4),
                                                                (2, 5),
                                                                (3, 6),
                                                                (3, 7),
                                                                (3, 8),
                                                                (3, 9),
                                                                (3, 10),
                                                                (3, 11),
                                                                (3, 14),
                                                                (4, 1),
                                                                (4, 2),
                                                                (4, 3),
                                                                (4, 4),
                                                                (4, 5),
                                                                (4, 6),
                                                                (4, 7),
                                                                (4, 8),
                                                                (4, 9),
                                                                (4, 10),
                                                                (4, 11),
                                                                (4, 12),
                                                                (4, 13),
                                                                (4, 14),
                                                                (4, 15);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Users`
--

CREATE TABLE `Users` (
                         `user_id` int(11) NOT NULL,
                         `first_name` varchar(50) NOT NULL,
                         `last_name` varchar(50) NOT NULL,
                         `email` varchar(100) NOT NULL,
                         `address_id` int(11) NOT NULL,
                         `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `Users`
--

INSERT INTO `Users` (`user_id`, `first_name`, `last_name`, `email`, `address_id`, `password_hash`) VALUES
                                                                                                       (1, 'Max', 'Mustermann', 'max.mustermann@email.de', 1, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890'),
                                                                                                       (2, 'Anna', 'Schmidt', 'anna.schmidt@email.de', 2, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567891'),
                                                                                                       (3, 'Peter', 'Wagner', 'peter.wagner@email.de', 3, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567892'),
                                                                                                       (4, 'Lisa', 'Becker', 'lisa.becker@email.de', 4, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567893'),
                                                                                                       (5, 'Thomas', 'Fischer', 'thomas.fischer@email.de', 5, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567894'),
                                                                                                       (6, 'Julia', 'Meyer', 'julia.meyer@email.de', 6, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567895'),
                                                                                                       (7, 'Michael', 'Weber', 'michael.weber@email.de', 7, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567896'),
                                                                                                       (8, 'Sarah', 'Schulz', 'sarah.schulz@email.de', 8, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567897'),
                                                                                                       (9, 'Daniel', 'Hoffmann', 'daniel.hoffmann@email.de', 9, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567898'),
                                                                                                       (10, 'Laura', 'Koch', 'laura.koch@email.de', 10, '$2y$10$abcdefghijklmnopqrstuvwxyz1234567899'),
                                                                                                       (11, 'user', 'manager', 'user.manager@email.de', 11, '$2y$10$zZ6Ck4nFQ1GbCSuBovI1.O3jdds..UdV.0BulWmIfe6CMFNLY9haW'),
                                                                                                       (16, 'emre', 'koc', 'e@e.com', 16, '$2y$10$7qj/h86fRdR/8Lc9rcUmjOgtBrjHuoTJfAAFz3xVusLUrM5a2JjZK'),
                                                                                                       (18, 'Product', 'manager', 'product.manager@email.de', 18, '$2y$10$zZ6Ck4nFQ1GbCSuBovI1.O3jdds..UdV.0BulWmIfe6CMFNLY9haW'),
                                                                                                       (19, 'User', 'nachname', 'user@email.de', 19, '$2y$10$60y3uDb76pjN/6lMHeOouuIUyFbK5KQLDxj9pG82rmZ8xiTlt3kni');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User_Roles`
--

CREATE TABLE `User_Roles` (
                              `user_id` int(11) NOT NULL,
                              `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `User_Roles`
--

INSERT INTO `User_Roles` (`user_id`, `role_id`) VALUES
                                                    (1, 1),
                                                    (2, 1),
                                                    (3, 2),
                                                    (4, 1),
                                                    (5, 3),
                                                    (6, 1),
                                                    (7, 1),
                                                    (8, 1),
                                                    (9, 1),
                                                    (10, 1),
                                                    (11, 3),
                                                    (16, 1),
                                                    (18, 2),
                                                    (19, 1);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Addresses`
--
ALTER TABLE `Addresses`
    ADD PRIMARY KEY (`address_id`);

--
-- Indizes für die Tabelle `Cart`
--
ALTER TABLE `Cart`
    ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `Categories`
--
ALTER TABLE `Categories`
    ADD PRIMARY KEY (`category_id`);

--
-- Indizes für die Tabelle `OrderItems`
--
ALTER TABLE `OrderItems`
    ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `Orders`
--
ALTER TABLE `Orders`
    ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_address_id` (`order_address_id`);

--
-- Indizes für die Tabelle `Order_Addresses`
--
ALTER TABLE `Order_Addresses`
    ADD PRIMARY KEY (`address_id`);

--
-- Indizes für die Tabelle `password_resets`
--
ALTER TABLE `password_resets`
    ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `Permissions`
--
ALTER TABLE `Permissions`
    ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indizes für die Tabelle `Products`
--
ALTER TABLE `Products`
    ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indizes für die Tabelle `Ratings`
--
ALTER TABLE `Ratings`
    ADD PRIMARY KEY (`rating_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `Roles`
--
ALTER TABLE `Roles`
    ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indizes für die Tabelle `Role_Permissions`
--
ALTER TABLE `Role_Permissions`
    ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
    ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `address_id` (`address_id`);

--
-- Indizes für die Tabelle `User_Roles`
--
ALTER TABLE `User_Roles`
    ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Addresses`
--
ALTER TABLE `Addresses`
    MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `Categories`
--
ALTER TABLE `Categories`
    MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `Orders`
--
ALTER TABLE `Orders`
    MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `Order_Addresses`
--
ALTER TABLE `Order_Addresses`
    MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `password_resets`
--
ALTER TABLE `password_resets`
    MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Permissions`
--
ALTER TABLE `Permissions`
    MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `Products`
--
ALTER TABLE `Products`
    MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `Ratings`
--
ALTER TABLE `Ratings`
    MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `Roles`
--
ALTER TABLE `Roles`
    MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Cart`
--
ALTER TABLE `Cart`
    ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`);

--
-- Constraints der Tabelle `OrderItems`
--
ALTER TABLE `OrderItems`
    ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`);

--
-- Constraints der Tabelle `Orders`
--
ALTER TABLE `Orders`
    ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`order_address_id`) REFERENCES `Order_Addresses` (`address_id`);

--
-- Constraints der Tabelle `password_resets`
--
ALTER TABLE `password_resets`
    ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `Products`
--
ALTER TABLE `Products`
    ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`);

--
-- Constraints der Tabelle `Ratings`
--
ALTER TABLE `Ratings`
    ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints der Tabelle `Role_Permissions`
--
ALTER TABLE `Role_Permissions`
    ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`role_id`),
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `Permissions` (`permission_id`);

--
-- Constraints der Tabelle `Users`
--
ALTER TABLE `Users`
    ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `Addresses` (`address_id`);

--
-- Constraints der Tabelle `User_Roles`
--
ALTER TABLE `User_Roles`
    ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
