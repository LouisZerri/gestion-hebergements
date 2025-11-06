'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import {
  Box,
  Button,
  Container,
  Heading,
  Input,
  VStack,
  HStack,
  Text,
  Textarea,
  Field,
  Grid,
  Spinner,
  Flex,
  Separator,
} from '@chakra-ui/react';
import { apiService } from '@/lib/api';
import { showToast } from '@/lib/toast';
import PhotoManager from '@/components/PhotoManager';
import type { HotelFormData, HotelPicture } from '@/types/api';

export default function EditHotelPage() {
  const params = useParams();
  const router = useRouter();
  const hotelId = Number(params.id);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [pictures, setPictures] = useState<HotelPicture[]>([]);

  const [formData, setFormData] = useState<HotelFormData>({
    name: '',
    address_1: '',
    address_2: '',
    zip_code: '',
    city: '',
    country: '',
    longitude: 0,
    latitude: 0,
    description: '',
    max_capacity: 1,
    price_per_night: 0,
  });

  // Charger les données de l'hôtel
  useEffect(() => {
    const loadHotel = async () => {
      try {
        const response = await apiService.getHotel(hotelId);
        if (response.success && response.data) {
          const hotel = response.data;
          setFormData({
            name: hotel.name,
            address_1: hotel.address_1,
            address_2: hotel.address_2 || '',
            zip_code: hotel.zip_code,
            city: hotel.city,
            country: hotel.country,
            longitude: hotel.longitude,
            latitude: hotel.latitude,
            description: hotel.description || '',
            max_capacity: hotel.max_capacity,
            price_per_night: hotel.price_per_night,
          });
          setPictures(hotel.pictures || []);
        }
      } catch (error: any) {
        showToast.error(error.message || 'Impossible de charger l\'hôtel');
        router.push('/');
      } finally {
        setLoading(false);
      }
    };

    loadHotel();
  }, [hotelId]);

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: ['longitude', 'latitude', 'max_capacity', 'price_per_night'].includes(name)
        ? Number(value)
        : value,
    }));
    if (errors[name]) {
      setErrors((prev) => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setErrors({});

    try {
      const response = await apiService.updateHotel(hotelId, formData);
      if (response.success && response.data) {
        showToast.success('Hôtel modifié avec succès !');
        // Ne pas rediriger pour permettre la gestion des photos
      }
    } catch (error: any) {
      if (error.errors) {
        setErrors(error.errors);
        showToast.error('Erreur de validation. Vérifiez les champs.');
      } else {
        showToast.error(error.message || 'Impossible de modifier l\'hôtel');
      }
    } finally {
      setSaving(false);
    }
  };

  const handleFinish = () => {
    router.push(`/hotels/${hotelId}`);
  };

  const handleCancel = () => {
    router.push(`/hotels/${hotelId}`);
  };

  if (loading) {
    return (
      <Container maxW="4xl" py={10}>
        <Flex justify="center" align="center" h="400px">
          <Spinner size="xl" color="blue.500" />
        </Flex>
      </Container>
    );
  }

  return (
    <Container maxW="4xl" py={8}>
      <VStack gap={6} align="stretch">
        {/* Header */}
        <HStack justify="space-between">
          <Heading size="lg">Modifier l'hôtel</Heading>
          <Button variant="outline" onClick={handleCancel}>
            Annuler
          </Button>
        </HStack>

        {/* Formulaire */}
        <Box
          as="form"
          onSubmit={handleSubmit}
          borderWidth="1px"
          borderRadius="lg"
          p={6}
        >
          <VStack gap={4} align="stretch">
            {/* Nom */}
            <Field.Root invalid={!!errors.name}>
              <Field.Label>Nom de l'hôtel *</Field.Label>
              <Input
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder="Ex: Hôtel du Centre"
                size="lg"
              />
              {errors.name && (
                <Field.ErrorText>{errors.name[0]}</Field.ErrorText>
              )}
            </Field.Root>

            {/* Adresse */}
            <Grid templateColumns={{ base: '1fr', md: '2fr 1fr' }} gap={4}>
              <Field.Root invalid={!!errors.address_1}>
                <Field.Label>Adresse 1 *</Field.Label>
                <Input
                  name="address_1"
                  value={formData.address_1}
                  onChange={handleChange}
                  placeholder="Ex: 123 Rue de la Paix"
                />
                {errors.address_1 && (
                  <Field.ErrorText>{errors.address_1[0]}</Field.ErrorText>
                )}
              </Field.Root>

              <Field.Root invalid={!!errors.address_2}>
                <Field.Label>Adresse 2</Field.Label>
                <Input
                  name="address_2"
                  value={formData.address_2}
                  onChange={handleChange}
                  placeholder="Appartement, suite..."
                />
                {errors.address_2 && (
                  <Field.ErrorText>{errors.address_2[0]}</Field.ErrorText>
                )}
              </Field.Root>
            </Grid>

            <Grid templateColumns={{ base: '1fr', md: '1fr 2fr' }} gap={4}>
              <Field.Root invalid={!!errors.zip_code}>
                <Field.Label>Code postal *</Field.Label>
                <Input
                  name="zip_code"
                  value={formData.zip_code}
                  onChange={handleChange}
                  placeholder="Ex: 75001"
                />
                {errors.zip_code && (
                  <Field.ErrorText>{errors.zip_code[0]}</Field.ErrorText>
                )}
              </Field.Root>

              <Field.Root invalid={!!errors.city}>
                <Field.Label>Ville *</Field.Label>
                <Input
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                  placeholder="Ex: Paris"
                />
                {errors.city && (
                  <Field.ErrorText>{errors.city[0]}</Field.ErrorText>
                )}
              </Field.Root>
            </Grid>

            <Field.Root invalid={!!errors.country}>
              <Field.Label>Pays *</Field.Label>
              <Input
                name="country"
                value={formData.country}
                onChange={handleChange}
                placeholder="Ex: France"
              />
              {errors.country && (
                <Field.ErrorText>{errors.country[0]}</Field.ErrorText>
              )}
            </Field.Root>

            {/* Coordonnées GPS */}
            <Grid templateColumns={{ base: '1fr', md: '1fr 1fr' }} gap={4}>
              <Field.Root invalid={!!errors.latitude}>
                <Field.Label>Latitude *</Field.Label>
                <Input
                  name="latitude"
                  type="number"
                  step="any"
                  value={formData.latitude}
                  onChange={handleChange}
                  placeholder="Ex: 48.8566"
                />
                <Text fontSize="xs" color="gray.600" mt={1}>
                  Entre -90 et 90
                </Text>
                {errors.latitude && (
                  <Field.ErrorText>{errors.latitude[0]}</Field.ErrorText>
                )}
              </Field.Root>

              <Field.Root invalid={!!errors.longitude}>
                <Field.Label>Longitude *</Field.Label>
                <Input
                  name="longitude"
                  type="number"
                  step="any"
                  value={formData.longitude}
                  onChange={handleChange}
                  placeholder="Ex: 2.3522"
                />
                <Text fontSize="xs" color="gray.600" mt={1}>
                  Entre -180 et 180
                </Text>
                {errors.longitude && (
                  <Field.ErrorText>{errors.longitude[0]}</Field.ErrorText>
                )}
              </Field.Root>
            </Grid>

            {/* Description */}
            <Field.Root invalid={!!errors.description}>
              <Field.Label>Description</Field.Label>
              <Textarea
                name="description"
                value={formData.description}
                onChange={handleChange}
                placeholder="Décrivez l'hôtel..."
                rows={4}
              />
              {errors.description && (
                <Field.ErrorText>{errors.description[0]}</Field.ErrorText>
              )}
            </Field.Root>

            {/* Capacité et prix */}
            <Grid templateColumns={{ base: '1fr', md: '1fr 1fr' }} gap={4}>
              <Field.Root invalid={!!errors.max_capacity}>
                <Field.Label>Capacité maximale *</Field.Label>
                <Input
                  name="max_capacity"
                  type="number"
                  min="1"
                  max="200"
                  value={formData.max_capacity}
                  onChange={handleChange}
                  placeholder="Ex: 50"
                />
                <Text fontSize="xs" color="gray.600" mt={1}>
                  Entre 1 et 200 personnes
                </Text>
                {errors.max_capacity && (
                  <Field.ErrorText>{errors.max_capacity[0]}</Field.ErrorText>
                )}
              </Field.Root>

              <Field.Root invalid={!!errors.price_per_night}>
                <Field.Label>Prix par nuit (€) *</Field.Label>
                <Input
                  name="price_per_night"
                  type="number"
                  step="0.01"
                  min="0"
                  value={formData.price_per_night}
                  onChange={handleChange}
                  placeholder="Ex: 150.00"
                />
                {errors.price_per_night && (
                  <Field.ErrorText>{errors.price_per_night[0]}</Field.ErrorText>
                )}
              </Field.Root>
            </Grid>

            {/* Bouton enregistrer */}
            <HStack justify="flex-end" pt={4}>
              <Button
                type="submit"
                colorScheme="blue"
                loading={saving}
              >
                Enregistrer les modifications
              </Button>
            </HStack>
          </VStack>
        </Box>

        {/* Section photos */}
        <Separator />
        <Box borderWidth="1px" borderRadius="lg" p={6}>
          <PhotoManager
            hotelId={hotelId}
            pictures={pictures}
            onPhotosChange={setPictures}
          />
        </Box>

        {/* Bouton terminer */}
        <HStack justify="flex-end">
          <Button variant="outline" onClick={handleCancel}>
            Annuler
          </Button>
          <Button colorScheme="green" size="lg" onClick={handleFinish}>
            Terminer et voir l'hôtel
          </Button>
        </HStack>

        <Text fontSize="sm" color="gray.600" textAlign="center">
          * Champs obligatoires
        </Text>
      </VStack>
    </Container>
  );
}